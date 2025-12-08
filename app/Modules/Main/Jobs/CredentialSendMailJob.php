<?php

namespace App\Modules\Main\Jobs;

use App\Modules\Main\Mail\CredentialSendMail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CredentialSendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;
    private $credentials;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $credentials = [])
    {
        $this->user = $user;
        $this->credentials = $credentials;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to($this->user)
                ->send(new CredentialSendMail($this->user, $this->credentials));
        } catch(Exception $e) {
            Log::error('SendEmailJob@handle - Mail Error:', [
                'to'    => $this->user->email,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
        }
    }
}
