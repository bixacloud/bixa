<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PopupNotification;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use \App\Mail\Admin\BulkNotification;
use Illuminate\Support\Facades\Auth;
use App\Models\SmtpSetting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class NotificationController extends Controller
{
    /**
     * Display the announcements page
     */
    public function announcements()
    {
        $announcements = Announcement::orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.notifications.announcements', compact('announcements'));
    }
    
    /**
     * Get active announcements limited to 3 for dashboard display
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveAnnouncementsForDashboard()
    {
        return Announcement::where('is_enabled', true)
            ->where(function($query) {
                $query->whereNull('start_date')
                      ->orWhere('start_date', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('end_date')
                      ->orWhere('end_date', '>=', now());
            })
            ->orderBy('display_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(3) // Limit to 3 announcements
            ->get();
    }
    
    /**
     * Store a new announcement
     */
    public function storeAnnouncement(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:info,success,warning,danger',
                'display_order' => 'required|integer|min:0',
            ]);
            
            $announcement = new Announcement();
            $announcement->title = $request->title;
            $announcement->content = $request->content;
            $announcement->type = $request->type;
            $announcement->display_order = $request->display_order;
            $announcement->icon = $request->icon;
            $announcement->is_enabled = $request->has('is_enabled');
            
            if ($request->start_date) {
                $announcement->start_date = $request->start_date;
            }
            
            if ($request->end_date) {
                $announcement->end_date = $request->end_date;
            }
            
            $announcement->save();
            
            return redirect()->route('admin.notifications.announcements')
                ->with('success', 'Announcement created successfully');
        } catch (\Exception $e) {
            Log::error('Failed to create announcement: ' . $e->getMessage());
            return redirect()->route('admin.notifications.announcements')
                ->with('error', 'Failed to create announcement: ' . $e->getMessage());
        }
    }
    
    /**
     * Get announcement data for editing
     */
    public function editAnnouncement($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            return response()->json([
                'announcement' => $announcement
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Announcement not found'
            ], 404);
        }
    }
    
    /**
     * Update an announcement
     */
    public function updateAnnouncement(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:info,success,warning,danger',
                'display_order' => 'required|integer|min:0',
            ]);
            
            $announcement = Announcement::findOrFail($id);
            $announcement->title = $request->title;
            $announcement->content = $request->content;
            $announcement->type = $request->type;
            $announcement->display_order = $request->display_order;
            $announcement->icon = $request->icon;
            $announcement->is_enabled = $request->has('is_enabled');
            
            $announcement->start_date = $request->start_date ?: null;
            $announcement->end_date = $request->end_date ?: null;
            
            $announcement->save();
            
            return redirect()->route('admin.notifications.announcements')
                ->with('success', 'Announcement updated successfully');
        } catch (\Exception $e) {
            Log::error('Failed to update announcement: ' . $e->getMessage());
            return redirect()->route('admin.notifications.announcements')
                ->with('error', 'Failed to update announcement');
        }
    }
    
    /**
     * Delete an announcement
     */
    public function deleteAnnouncement($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->delete();
            
            return redirect()->route('admin.notifications.announcements')
                ->with('success', 'Announcement deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.notifications.announcements')
                ->with('error', 'Failed to delete announcement');
        }
    }
    
    /**
     * Toggle announcement visibility
     */
    public function toggleAnnouncement($id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $announcement->is_enabled = !$announcement->is_enabled;
            $announcement->save();
            
            return response()->json([
                'success' => true,
                'enabled' => $announcement->is_enabled
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to toggle announcement'
            ], 500);
        }
    }
    
    /**
     * Display popup notifications page
     */
    public function popups()
    {
        $popups = PopupNotification::with('dismissedByUsers')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.notifications.popups', compact('popups'));
    }
    
    /**
     * Store a new popup notification
     */
    public function storePopup(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:info,success,warning,danger',
            ]);
            
            // Create new popup
            $popup = new PopupNotification();
            $popup->title = $request->title;
            $popup->content = $request->content;
            $popup->type = $request->type;
            $popup->is_enabled = $request->has('is_enabled');
            $popup->allow_dismiss = $request->has('allow_dismiss');
            $popup->show_once = $request->has('show_once');
            
            // Handle dates
            $popup->start_date = $request->start_date ?: null;
            $popup->end_date = $request->end_date ?: null;
            
            $popup->save();
            
            return redirect()->route('admin.notifications.popups')
                ->with('success', 'Popup notification created successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error creating popup notification: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('admin.notifications.popups')
                ->with('error', 'Failed to create popup notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Get popup notification for editing
     */
    public function editPopup($id)
    {
        try {
            $popup = PopupNotification::findOrFail($id);
            return response()->json([
                'popup' => $popup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Popup notification not found'
            ], 404);
        }
    }
    
    /**
     * Update an existing popup notification
     */
    public function updatePopup(Request $request, $id)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'type' => 'required|in:info,success,warning,danger',
            ]);
            
            // Find popup
            $popup = PopupNotification::findOrFail($id);
            
            // Update popup
            $popup->title = $request->title;
            $popup->content = $request->content;
            $popup->type = $request->type;
            $popup->is_enabled = $request->has('is_enabled');
            $popup->allow_dismiss = $request->has('allow_dismiss');
            $popup->show_once = $request->has('show_once');
            
            // Handle dates
            $popup->start_date = $request->start_date ?: null;
            $popup->end_date = $request->end_date ?: null;
            
            $popup->save();
            
            return redirect()->route('admin.notifications.popups')
                ->with('success', 'Popup notification updated successfully');
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error updating popup notification: ' . $e->getMessage());
            
            return redirect()->route('admin.notifications.popups')
                ->with('error', 'Failed to update popup notification');
        }
    }
    
    /**
     * Delete a popup notification
     */
    public function deletePopup($id)
    {
        try {
            $popup = PopupNotification::findOrFail($id);
            $popup->delete();
            
            return redirect()->route('admin.notifications.popups')
                ->with('success', 'Popup notification deleted successfully');
        } catch (\Exception $e) {
            return redirect()->route('admin.notifications.popups')
                ->with('error', 'Failed to delete popup notification');
        }
    }
    
    /**
     * Toggle popup active status
     */
    public function togglePopup($id)
    {
        try {
            $popup = PopupNotification::findOrFail($id);
            $popup->is_enabled = !$popup->is_enabled;
            $popup->save();
            
            return response()->json([
                'success' => true,
                'enabled' => $popup->is_enabled,
                'message' => 'Popup status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to toggle popup status'
            ], 500);
        }
    }
    
    /**
     * Display email notification form
     */
    public function emailForm()
    {
        $userCount = User::count();
        $smtpSettings = SmtpSetting::first();
        
        return view('admin.notifications.bulk-email', compact('userCount', 'smtpSettings'));
    }
    
    /**
     * Send bulk email notification
     */
    public function sendEmail(Request $request)
    {
        try {
            $request->validate([
                'subject' => 'required|string|max:255',
                'content' => 'required|string',
            ]);
            
            // Check SMTP settings
            $smtpSettings = SmtpSetting::first();
            if (!$smtpSettings || !$smtpSettings->status) {
                return redirect()->route('admin.notifications.bulk-email')
                    ->with('error', 'SMTP is not configured or disabled.');
            }
            
            $subject = $request->subject;
            $content = $request->content;
            $testEmail = $request->has('test_email');
            
            // Add log for debugging
            \Log::info("Sending email with subject: $subject");
            \Log::info("Content length: " . strlen($content) . " characters");
            
            if ($testEmail) {
                // Send test email to admin only
                $admin = Auth::user();
                
                try {
                    // Make sure to use the correct namespace
                    Mail::to($admin->email)->send(new \App\Mail\Admin\BulkNotification($subject, $content));
                    \Log::info("Test email sent successfully to admin: " . $admin->email);
                    
                    return redirect()->route('admin.notifications.bulk-email')
                        ->with('success', 'Test email sent successfully.');
                } catch (\Exception $e) {
                    \Log::error("Error sending test email: " . $e->getMessage());
                    \Log::error($e->getTraceAsString());
                    
                    return redirect()->route('admin.notifications.bulk-email')
                        ->with('error', 'Failed to send test email: ' . $e->getMessage());
                }
            } else {
                // Send bulk email to all users
                $users = User::all();
                $sendCount = 0;
                
                foreach ($users as $user) {
                    try {
                        Mail::to($user->email)->queue(new \App\Mail\Admin\BulkNotification($subject, $content));
                        $sendCount++;
                    } catch (\Exception $e) {
                        \Log::error("Error sending email to {$user->email}: " . $e->getMessage());
                    }
                }
                
                return redirect()->route('admin.notifications.bulk-email')
                    ->with('success', "Email notifications queued for sending to $sendCount out of " . count($users) . " users.");
            }
        } catch (\Exception $e) {
            Log::error('Failed to send email notification: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->route('admin.notifications.bulk-email')
                ->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
    
    /**
     * Display notification settings
     */
    public function settings()
    {
        // Get current notification settings
        $notificationConfig = config('notifications');
        
        return view('admin.notifications.settings', [
            'settings' => $notificationConfig
        ]);
    }
    
    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $request->validate([
                'cleanup_days' => 'required|integer|min:1|max:365',
                'cleanup_probability' => 'required|integer|min:1|max:100',
                'cleanup_batch_size' => 'required|integer|min:10|max:1000',
                'floating_panel_limit' => 'required|integer|min:1|max:20',
                'check_interval' => 'required|integer|min:10000|max:600000',
            ]);
            
            // Get current configuration
            $configPath = config_path('notifications.php');
            
            // Update configuration values
            $config = [
                'cleanup_days' => (int) $request->cleanup_days,
                'cleanup_probability' => (int) $request->cleanup_probability,
                'cleanup_batch_size' => (int) $request->cleanup_batch_size,
                'floating_panel_limit' => (int) $request->floating_panel_limit,
                'check_interval' => (int) $request->check_interval,
                'types' => config('notifications.types', [
                    'login' => [
                        'icon' => 'bx bx-log-in',
                        'color' => 'primary',
                    ],
                    'hosting' => [
                        'icon' => 'bx bx-server',
                        'color' => 'success',
                    ],
                    'ticket' => [
                        'icon' => 'bx bx-support',
                        'color' => 'info',
                    ],
                    'ssl' => [
                        'icon' => 'bx bx-lock-alt',
                        'color' => 'warning',
                    ],
                    'account' => [
                        'icon' => 'bx bx-user',
                        'color' => 'secondary',
                    ],
                    'system' => [
                        'icon' => 'bx bx-bell',
                        'color' => 'primary',
                    ],
                ]),
            ];
            
            // Update config in memory for current request
            config(['notifications' => $config]);
            
            // Generate config file content
            $configContent = "<?php\n\nreturn " . $this->varExport($config) . ";\n";
            
            // Write the new configuration
            File::put($configPath, $configContent);
            
            // Manual cleanup trigger if requested
            if ($request->has('run_cleanup')) {
                // Get notification model
                $notificationModel = app(\App\Models\Notification::class);
                
                // Run cleanup with 100% probability (force)
                $cleanedCount = $notificationModel->deleteOldNotifications(
                    $config['cleanup_days'],
                    $config['cleanup_batch_size']
                );
                
                return redirect()->route('admin.notifications.settings')
                    ->with('success', "Settings updated successfully. Cleaned up $cleanedCount old notifications.");
            }
            
            return redirect()->route('admin.notifications.settings')
                ->with('success', 'Notification settings updated successfully.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update notification settings: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return redirect()->route('admin.notifications.settings')
                ->with('error', 'Failed to update notification settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper to create a pretty formatted array export
     * 
     * @param mixed $var The variable to export
     * @param string $indent The indentation
     * @return string
     */
    private function varExport($var, $indent = "    ")
    {
        switch (gettype($var)) {
            case 'string':
                return "'" . addcslashes($var, "'\\\r\n") . "'";
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = "$indent    "
                         . ($indexed ? "" : $this->varExport($key) . " => ")
                         . $this->varExport($value, "$indent    ");
                }
                return "[\n" . implode(",\n", $r) . "\n$indent]";
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'integer':
            case 'double':
                return $var;
            default:
                return var_export($var, true);
        }
    }
}