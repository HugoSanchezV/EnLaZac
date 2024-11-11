<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Http\CurlClient;

class SMSService
{

    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->twilio = new Client($sid, $token);
    }

    public function sendSMS($to, $message)
    {
        Log::info("entre al metodo");
        return $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message,
        ]);
    }

    public function MessengerWhatsapp(){
        $sid    = "AC733981076738ed1ad780e45c8743eb2f";
        $token  = env('TWILIO_AUTH_TOKEN');
        $twilio = new Client($sid, $token);
    
        $message = $twilio->messages
          ->create("whatsapp:+5214931704490", // to
            array(
              "from" => "whatsapp:+14155238886",
              "contentSid" => "HX350d429d32e64a552466cafecbe95f3c",
              "contentVariables" => '{"1":"12/1","2":"3pm"}',
              "body" => "Your Message"
            )
          );
    
    print($message->sid);
    }
}