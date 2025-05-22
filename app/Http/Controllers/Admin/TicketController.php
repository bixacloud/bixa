<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Coderflex\LaravelTicket\Models\Ticket;
use Coderflex\LaravelTicket\Models\Category;
use App\Mail\Ticket\TicketStatusChangedMail;
use App\Mail\Ticket\TicketReplyMail;
use App\Models\StaffRating;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'category'])->latest()->paginate(10);
        return view('admin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'category', 'messages.user']);
        return view('admin.tickets.show', compact('ticket'));
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:open,closed,pending',
            ]);

            DB::beginTransaction();
            
            $oldStatus = $ticket->status;
            $ticket->update($validated);

            // Send email notification
            try {
                Mail::to($ticket->user->email)
                    ->send(new TicketStatusChangedMail($ticket, $oldStatus));

                \Log::info('Admin sent ticket status change email', [
                    'ticket_id' => $ticket->id,
                    'user_email' => $ticket->user->email
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to send ticket status email', [
                    'ticket_id' => $ticket->id,
                    'error' => $e->getMessage()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Ticket status updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating ticket status', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Error updating ticket status');
        }
    }

   public function reply(Request $request, Ticket $ticket)
{
    try {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        DB::beginTransaction();

        $user = Auth::user();
        $messageContent = $validated['message'];
        
        // Append signature for admin/support if exists
        if (in_array($user->role, ['admin', 'support']) && !empty($user->signature)) {
            $messageContent .= "\n\n<div style=\"margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;\">\n" . $user->signature . "\n</div>";
        }

        // Create message
        $message = $ticket->messages()->create([
            'user_id' => $user->id,
            'message' => $messageContent,
        ]);

        // Update ticket status
        $oldStatus = $ticket->status;
        $ticket->update(['status' => 'answered']);

        // Send email notification
        try {
            Mail::to($ticket->user->email)
                ->send(new TicketReplyMail($ticket, $message));
        } catch (\Exception $e) {
            \Log::error('Failed to send ticket reply email', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
        }

        DB::commit();
        return redirect()->back()->with('success', 'Reply sent successfully');

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()->with('error', 'Error sending reply');
    }
}
    
    /**
     * Display the ratings dashboard with summary of all admin ratings.
     * 
     * @return \Illuminate\View\View
     */
    public function ratings()
    {
        // Get all admins who have received ratings
        $admins = User::whereHas('receivedRatings')
            ->withCount('receivedRatings')
            ->withAvg('receivedRatings as average_rating', 'rating')
            ->orderByDesc('average_rating')
            ->get();
            
        // Get overall stats
        $totalRatings = StaffRating::count();
        $averageRating = StaffRating::avg('rating');
        
        // Get rating distribution
        $ratingDistribution = StaffRating::select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
            
        // Fill in missing ratings with zero
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = 0;
            }
        }
        
        // Sort by rating in descending order
        krsort($ratingDistribution);
        
        return view('admin.tickets.ratings', compact('admins', 'totalRatings', 'averageRating', 'ratingDistribution'));
    }
    
    /**
     * Display detailed ratings for a specific admin.
     * 
     * @param int $adminId
     * @return \Illuminate\View\View
     */
    public function adminRatings($adminId)
    {
        $admin = User::findOrFail($adminId);
        
        // Get admin's ratings
        $ratings = StaffRating::with(['ticket', 'message', 'user'])
            ->where('admin_id', $adminId)
            ->latest()
            ->paginate(10);
            
        // Get admin stats
        $totalRatings = $ratings->total();
        $averageRating = $admin->receivedRatings()->avg('rating');
        
        // Get rating distribution for this admin
        $ratingDistribution = StaffRating::where('admin_id', $adminId)
            ->select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->pluck('count', 'rating')
            ->toArray();
            
        // Fill in missing ratings with zero
        for ($i = 1; $i <= 5; $i++) {
            if (!isset($ratingDistribution[$i])) {
                $ratingDistribution[$i] = 0;
            }
        }
        
        // Sort by rating in descending order
        krsort($ratingDistribution);
        
        return view('admin.tickets.admin_ratings', compact('admin', 'ratings', 'totalRatings', 'averageRating', 'ratingDistribution'));
    }
}