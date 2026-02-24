<?php

namespace App\Jobs;

use App\Traits\DataTransfer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, DataTransfer;

    public $payload;
    public function __construct($payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        info("sending sms..........");
        $this->postRequest(env('FLEXSAKO_BASE_URL').'v1/flex-investor/send-sms',$this->payload);
    }
}
