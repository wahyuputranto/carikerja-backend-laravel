<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $application;
    public $status;
    public $message;

    public function __construct(Application $application, $status, $message)
    {
        $this->application = $application;
        $this->status = $status;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $jobTitle = $this->application->job ? $this->application->job->title : 'Job Application';
        
        return (new MailMessage)
                    ->subject("Application Update: {$jobTitle}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Your application for the position of **{$jobTitle}** has been updated.")
                    ->line("Current Status: **{$this->status}**")
                    ->line($this->message)
                    ->action('View Application', url('/'))
                    ->line('Thank you for using our platform!');
    }
}
