<?php

namespace App\Modules\Main\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Http;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $config;
    private $data;
    
    public function __construct($data = [], $config = [])
    {
        $this->data = (object) $data;

        if (!empty($config)) {
            $this->config = (object) $config;
        } else {
            $this->config = (object) config("common.sms_service");
        }
    }

    /**
     * Execute the job.
     *
     * @return boolean
     */
    public function handle(): void
    {
        try {

            if (empty($this->data->msisdn) || empty($this->data->sms_body)) {
                Log::error("SendSmsJob@handle - Validation Error: msisdn & sms_body data are required! requested - " . json_encode($this->data));
                return;
            }

            if (empty($this->config) || empty($this->config->api_url) || empty($this->config->api_token)) {
                Log::error("SendSmsJob@handle - no configuration found, please check the sms_service configuration. - " . json_encode($this->config));
                return;
            }
    
            # OTHER SMS GATEWAY
            # ---------------------------------------------------------
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json'
            ])->post(config('common.sms_service.api_url'), [
                "api_token" => config('common.sms_service.api_token'),
                "sid"       => config('common.sms_service.sid'),
                "csms_id"   => str()->random(10),
                "msisdn"    => msisdn($this->data->msisdn),
                "sms"       => $this->data->sms_body
            ]);

            $data = $response->json();

            if ($response->successful() && !empty($data['status']) && strtolower($data['status']) == "success") {
                Log::debug("SendSmsJob@handle - Success: ".  $response->body());
            } else {
                Log::error("SendSmsJob@handle - Error: " . $response->body());
            }

        } catch(Exception $e) {
            Log::error("SendSmsJob@handle - Error: ". $e->getMessage() . ", Line - ".$e->getLine());
            return;
        }
    }

}
