<?php

namespace App\Modules\Main\Jobs;

use App\Modules\Main\Mail\SendEmail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mail;

    /**
     * Create a new job instance.
     */
    public function __construct($mail = [])
    {
        $this->mail = (object)$mail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->mail->to)
                ->send(new SendEmail($this->mail));
        } catch(Exception $e) {
            Log::error('SendEmailJob@handle - Mail Error:', [
                'to' => $this->mail->to,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
        }
    }
}
