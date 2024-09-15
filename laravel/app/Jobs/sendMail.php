<?php

namespace App\Jobs;

use App\Mail\Mail as MailMail;
use App\Mail\SendMailActivation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class sendMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $user;
    /**
     * Create a new job instance.
     */
    public function __construct($url, $user)
    {
        $this->url = $url;
        $this->user = $user;
    }
   

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send mail to user
        Mail::to($this->user->email)->send(new SendMailActivation ($this->url, $this->user));
    }
}
