<?php

namespace App\Services;

use App\Models\SMSSetting;
use Exception;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Twilio\Http\CurlClient;

class SMSService
{

  protected $twilio;
  protected $service;

  public function __construct()
  {
    try {
      $this->service = SMSSetting::all()->first();
      $sid = $this->service->account_sid;
      $token = $this->service->auth_token;
      $this->twilio = new Client($sid, $token);
    } catch (Exception $e) {
      Log::info('Error ' . $e->getMessage());
    }
  }

  public function sendSMS($to, $message)
  {
    try {
      if ($this->service->active) {
        return $this->twilio->messages->create($to, [
          'from' => $this->service->phone_number,
          'body' => $message,
        ]);
      } else {
        Log::info('Error sistema TWILIO no encendido');
      }
    } catch (Exception $e) {
      Log::info('Error ' . $e->getMessage());
    }
  }

  public function MessengerWhatsapp()
  {
    // $sid    = "AC733981076738ed1ad780e45c8743eb2f";
    // $token  = env('TWILIO_AUTH_TOKEN');
    // $twilio = new Client($sid, $token);

    // $message = $twilio->messages
    //   ->create(
    //     "whatsapp:+5214931704490", // to
    //     array(
    //       "from" => "whatsapp:+14155238886",
    //       "contentSid" => "HX350d429d32e64a552466cafecbe95f3c",
    //       "contentVariables" => '{"1":"12/1","2":"3pm"}',
    //       "body" => "Your Message"
    //     )
    //   );

    // print($message->sid);
  }
}
