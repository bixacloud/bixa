<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\Auth\NewDeviceLoginMail;
use App\Mail\Auth\FailedLoginMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestAuthLogMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-auth-log {email} {type=new-device}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending Auth Log email notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->argument('type');
        
        $this->info("Sending test mail to: {$email}");
        
        $testData = [
            'time' => now()->format('Y-m-d H:i:s'),
            'ip_address' => request()->ip() ?? '127.0.0.1',
            'browser' => 'Test Browser',
            'device' => 'Test Device',
            'location' => 'Test Location'
        ];

        try {
            if ($type === 'new-device') {
                Mail::to($email)->send(new NewDeviceLoginMail(null, $testData));
                $this->info('New device login mail sent successfully!');
            } else if ($type === 'failed-login') {
                Mail::to($email)->send(new FailedLoginMail(null, $testData));
                $this->info('Failed login mail sent successfully!');
            } else {
                $this->error('Invalid mail type. Use "new-device" or "failed-login"');
                return 1;
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error sending mail: ' . $e->getMessage());
            Log::error('Error sending test auth log mail', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }
}