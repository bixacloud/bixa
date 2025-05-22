<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Category;
use Coderflex\LaravelTicket\Models\Message;
use App\Mail\Ticket\NewTicketMail;
use App\Mail\Ticket\TicketReplyMail;
use App\Mail\Ticket\TicketStatusChangedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\IconCaptchaSetting;
use App\Models\StaffRating;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;

class TicketController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with(['category'])
            ->latest()
            ->paginate(10);
            
        return view('tickets.index', compact('tickets'));
    }

    public function create(Request $request)
    {
        $categories = Category::all();

        // Only get active accounts or ones suspended by admin
        $hostingAccounts = DB::table('hosting_accounts')
            ->where('user_id', auth()->id())
            ->where(function($query) {
                $query->where('status', 'active')
                    ->orWhere('admin_deactivated', true); // Only get accounts suspended by admin
            })
            ->select('id', 'domain', 'label', 'username', 'status', 'admin_deactivated', 'admin_deactivation_reason')
            ->get();

        $certificates = DB::table('certificates')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['active', 'pending'])
            ->select('id', 'domain', 'type', 'status')
            ->get();

        // Check if there's information from a suspended account in the session
        $prefilled = null;
        
        // Get information from session if redirected from hosting page
        if (session()->has('admin_deactivated_account')) {
            $account = session('admin_deactivated_account');
            
            // Find hosting account from username
            $hostingAccount = DB::table('hosting_accounts')
                ->where('user_id', auth()->id())
                ->where('username', $account['username'])
                ->first();
                
            if ($hostingAccount) {
                $prefilled = [
                    'title' => 'Request to reactivate suspended hosting account: ' . $account['domain'],
                    'service_type' => 'hosting',
                    'hosting_id' => $hostingAccount->id,
                    'priority' => 'high',
                    'message' => "I would like to request reactivation of my hosting account that was suspended by an administrator.\n\n" .
                               "Account Details:\n" .
                               "- Username: {$account['username']}\n" .
                               "- Domain: {$account['domain']}\n" .
                               "- Suspended on: " . ($account['deactivated_at'] ? date('Y-m-d H:i:s', strtotime($account['deactivated_at'])) : 'N/A') . "\n\n" .
                               "Reason for suspension: " . ($account['deactivation_reason'] ?? 'Not specified') . "\n\n" .
                               "I would like this account to be reactivated because: [Please explain why your account should be reactivated]\n\n" .
                               "Thank you for your assistance."
                ];
            }
        }

        return view('tickets.create', [
            'categories' => $categories,
            'hostingAccounts' => $hostingAccounts,
            'certificates' => $certificates,
            'prefilled' => $prefilled
        ]);
    }

    public function store(Request $request)
    {
        Log::info('Ticket store called with data:', $request->except(['message', 'password']));

        try {
            // Check CAPTCHA verification if enabled
            if (IconCaptchaSetting::isEnabled('enabled', false)) {
                // Check for hp flag (client-side validation marker)
                if ($request->input('ic-hp') !== '1') {
                    return back()->withErrors(['error' => 'Please complete the CAPTCHA verification first.'])
                                ->withInput();
                }
                
                Log::info('Captcha verification passed for ticket creation', [
                    'user_id' => auth()->id(),
                    'captcha_data' => [
                        'hp' => $request->input('ic-hp'),
                        'wid' => $request->input('ic-wid')
                    ]
                ]);
            }

            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'message' => 'required|string',
                'category_id' => 'required|exists:categories,id',
                'priority' => 'required|in:low,medium,high',
                'service_type' => 'required|in:hosting,ssl',
                'hosting_id' => $request->service_type === 'hosting' ? 'required|exists:hosting_accounts,id' : 'nullable',
                'certificate_id' => $request->service_type === 'ssl' ? 'required|exists:certificates,id' : 'nullable',
            ]);

            // Check if it's an account suspended by admin
            if ($request->service_type === 'hosting' && $validated['hosting_id']) {
                $account = DB::table('hosting_accounts')
                    ->where('id', $validated['hosting_id'])
                    ->where('user_id', auth()->id())
                    ->first();
                    
                if ($account && $account->admin_deactivated) {
                    Log::info('Creating ticket for admin-deactivated account', [
                        'account_id' => $account->id,
                        'username' => $account->username
                    ]);
                }
            }

            DB::beginTransaction();

            // Create ticket
            $ticket = Ticket::create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'category_id' => $validated['category_id'],
                'priority' => $validated['priority'],
                'status' => 'open',
                'service_type' => $validated['service_type'],
                'service_id' => $validated['service_type'] === 'hosting'
                    ? $validated['hosting_id']
                    : $validated['certificate_id']
            ]);

            // Create first message
            $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $validated['message']
            ]);

            // Create ticket notification
            $this->notificationService->createTicketNotification(
                auth()->user(), 
                'created', 
                [
                    'ticket_id' => $ticket->id
                ]
            );

            // Send email notifications
            try {
                // Send to user
                Mail::to(auth()->user()->email)->send(new NewTicketMail($ticket));

                // Send to admin and support staff
                $staffEmails = User::whereIn('role', ['admin', 'support'])->pluck('email')->toArray();
                if (!empty($staffEmails)) {
                    Mail::to($staffEmails)->send(new NewTicketMail($ticket));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send ticket emails: ' . $e->getMessage());
                // Continue execution even if email fails
            }

            DB::commit();

            return redirect()->route('user.tickets.show', $ticket)
                ->with('success', 'Ticket created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating ticket: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating ticket: ' . $e->getMessage());
        }
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load(['category', 'messages.user']);
        return view('tickets.show', compact('ticket'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // Check CAPTCHA verification if enabled
            if (IconCaptchaSetting::isEnabled('enabled', false)) {
                // Check for hp flag (client-side validation marker)
                if ($request->input('ic-hp') !== '1') {
                    return back()->withErrors(['error' => 'Please complete the CAPTCHA verification first.'])
                                ->withInput();
                }
                
                Log::info('Captcha verification passed for ticket reply', [
                    'user_id' => auth()->id(),
                    'ticket_id' => $ticket->id,
                    'captcha_data' => [
                        'hp' => $request->input('ic-hp'),
                        'wid' => $request->input('ic-wid')
                    ]
                ]);
            }

            $validated = $request->validate([
                'message' => 'required|string',
            ]);

            Log::info('Creating ticket reply', [
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'message' => $validated['message']
            ]);

            DB::beginTransaction();

            // Create message
            $message = $ticket->messages()->create([
                'user_id' => auth()->id(),
                'message' => $validated['message'],
            ]);

            // Update ticket status to 'customer-reply'
            $oldStatus = $ticket->status;
            $ticket->update(['status' => 'customer-reply']);

            Log::info('Message created and status updated', [
                'message_id' => $message->id,
                'old_status' => $oldStatus,
                'new_status' => 'customer-reply'
            ]);

            // Send email notifications
            try {
                // Send to staff (admin and support)
                $staffEmails = User::whereIn('role', ['admin', 'support'])->pluck('email')->toArray();
                if (!empty($staffEmails)) {
                    Mail::to($staffEmails)->send(new TicketReplyMail($ticket, $message));
                    Log::info('Sent reply notification to staff', [
                        'staff_emails' => $staffEmails
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to send ticket reply emails', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Reply sent successfully');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error replying to ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error sending reply: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            $request->validate([
                'status' => 'required|in:open,closed,pending'
            ]);

            Log::info('Updating ticket status', [
                'ticket_id' => $ticket->id,
                'old_status' => $ticket->status,
                'new_status' => $request->status
            ]);

            DB::beginTransaction();

            $oldStatus = $ticket->status;
            $ticket->status = $request->status;
            $ticket->save();

            // Create ticket status change notification
            $this->notificationService->createTicketNotification(
                auth()->user(), 
                'status_changed', 
                [
                    'ticket_id' => $ticket->id,
                    'status' => $request->status
                ]
            );

            // Send email notification
            try {
                // Send to staff (admin and support)
                $staffEmails = User::whereIn('role', ['admin', 'support'])->pluck('email')->toArray();
                if (!empty($staffEmails)) {
                    Mail::to($staffEmails)->send(new TicketStatusChangedMail($ticket, $oldStatus));
                    Log::info('Sent status change notification to staff', [
                        'staff_emails' => $staffEmails
                    ]);
                }

            } catch (\Exception $e) {
                Log::error('Failed to send status change email', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', "Ticket status updated to {$request->status}");

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating ticket status', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()
                ->with('error', 'Error updating ticket status: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a rating for an admin's response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $messageId
     * @return \Illuminate\Http\Response
     */
    public function rateAdminResponse(Request $request, $messageId)
    {
        try {
            $validated = $request->validate([
                'rating' => 'required|integer|between:1,5',
                'comment' => 'nullable|string|max:1000',
                'ticket_id' => 'required|exists:tickets,id',
                'admin_id' => 'required|exists:users,id',
            ]);

            Log::info('Processing staff rating submission', [
                'user_id' => auth()->id(),
                'message_id' => $messageId,
                'ticket_id' => $validated['ticket_id'],
                'admin_id' => $validated['admin_id'],
                'rating' => $validated['rating']
            ]);

            // Check if message exists and belongs to the ticket
            $message = Message::findOrFail($messageId);
            $ticket = Ticket::findOrFail($validated['ticket_id']);
            
            // Ensure user can only rate tickets they own
            if ($ticket->user_id !== auth()->id()) {
                Log::warning('Unauthorized rating attempt', [
                    'user_id' => auth()->id(),
                    'ticket_id' => $validated['ticket_id']
                ]);
                return redirect()->back()->with('error', 'You can only rate tickets that belong to you.');
            }
            
            // Ensure message belongs to this ticket
            if ($message->ticket_id != $ticket->id) {
                Log::warning('Invalid message for rating', [
                    'message_id' => $messageId,
                    'ticket_id' => $validated['ticket_id']
                ]);
                return redirect()->back()->with('error', 'Invalid message for this ticket.');
            }
            
            DB::beginTransaction();
            
            // Create or update rating
            StaffRating::updateOrCreate(
                [
                    'message_id' => $messageId,
                    'user_id' => auth()->id()
                ],
                [
                    'ticket_id' => $validated['ticket_id'],
                    'admin_id' => $validated['admin_id'],
                    'rating' => $validated['rating'],
                    'comment' => $validated['comment']
                ]
            );
            
            DB::commit();
            
            Log::info('User submitted rating for admin', [
                'user_id' => auth()->id(),
                'admin_id' => $validated['admin_id'],
                'ticket_id' => $ticket->id,
                'message_id' => $messageId,
                'rating' => $validated['rating']
            ]);
            
            return redirect()->back()->with('success', 'Thank you for your feedback! Your rating has been recorded.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error submitting rating', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'message_id' => $messageId,
                'user_id' => auth()->id()
            ]);
            
            return redirect()->back()->with('error', 'There was a problem submitting your rating. Please try again.');
        }
    }
}