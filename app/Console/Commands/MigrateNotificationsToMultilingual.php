<?php

namespace App\Console\Commands;

use App\Models\UserNotification;
use Illuminate\Console\Command;

class MigrateNotificationsToMultilingual extends Command
{
    protected $signature = 'notifications:migrate-multilingual';
    protected $description = 'Migrate existing notifications to use multilingual keys';

    public function handle()
    {
        $this->info('Starting migration of existing notifications...');
        
        // Define patterns to map existing notifications to translation keys
        $patterns = [
            // Login notifications
            'Successful Login' => [
                'title_key' => 'notifications.login.title',
                'type' => 'login'
            ],
            '/^You logged in successfully from IP address (.+)$/' => [
                'content_key' => 'notifications.login.content',
                'params' => ['ip' => '$1'],
                'type' => 'login'
            ],
            
            // Password notifications
            'Password Changed' => [
                'title_key' => 'notifications.account.password_changed.title',
                'type' => 'account'
            ],
            '/^Your account password has been changed successfully/' => [
                'content_key' => 'notifications.account.password_changed.content',
                'type' => 'account'
            ],
            
            // Add more patterns for other notification types...
        ];
        
        $count = 0;
        $total = UserNotification::count();
        
        // Process notifications in batches
        UserNotification::chunk(100, function ($notifications) use ($patterns, &$count, $total) {
            foreach ($notifications as $notification) {
                $updated = false;
                $params = [];
                
                // Match exact titles
                if (isset($patterns[$notification->title])) {
                    $config = $patterns[$notification->title];
                    $notification->title_key = $config['title_key'];
                    if (isset($config['type'])) {
                        $notification->type = $config['type'];
                    }
                    $updated = true;
                }
                
                // Match content using regex patterns
                foreach ($patterns as $pattern => $config) {
                    if ($pattern[0] === '/' && isset($config['content_key'])) {
                        if (preg_match($pattern, $notification->content, $matches)) {
                            $notification->content_key = $config['content_key'];
                            
                            // Extract parameters
                            if (isset($config['params'])) {
                                foreach ($config['params'] as $key => $value) {
                                    if (preg_match('/^\$(\d+)$/', $value, $paramMatches)) {
                                        $index = (int)$paramMatches[1];
                                        $params[$key] = $matches[$index] ?? $value;
                                    }
                                }
                            }
                            
                            if (isset($config['type']) && !$notification->type) {
                                $notification->type = $config['type'];
                            }
                            
                            $updated = true;
                            break;
                        }
                    }
                }
                
                if ($updated) {
                    $notification->params = !empty($params) ? json_encode($params) : null;
                    $notification->save();
                    $count++;
                    
                    $this->output->write("\rProcessed: $count/$total");
                }
            }
        });
        
        $this->newLine();
        $this->info("Migration completed. Updated $count notifications out of $total.");
        
        return Command::SUCCESS;
    }
}