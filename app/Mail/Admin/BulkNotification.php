<?php
namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MailTemplates\TemplateMailable;

class BulkNotification extends TemplateMailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    // Các biến public sẽ được sử dụng trong template
    public $emailContent;
    
    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $content)
    {
        $this->subject = $subject;
        $this->emailContent = $content;
        
        // Thêm log để debug
        \Log::info("BulkNotification created with subject: '$subject', content length: " . strlen($content));
    }
    
    /**
     * Force the system to use template from database
     */
    public function getHtmlLayout(): ?string
    {
        return null; // Return null to force using template from DB
    }
    
    /**
     * Build the message.
     */
    public function build()
    {
        \Log::info('Building BulkNotification email');
        
        
        return $this;
    }
}