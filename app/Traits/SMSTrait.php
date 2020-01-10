<?php

namespace App\Traits;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use MobilySms;
//use Twilio\Rest\Client;
//use Twilio\Exceptions\TwilioException;

trait SMSTrait
{
   /* private function _notifyThroughSms($e)
    {
        foreach ($this->_notificationRecipients() as $recipient) {
            $this->sendSms(
                $recipient->phone_number,
                '[This is a test] It appears the server' .
                ' is having issues. Exception: ' . $e->getMessage() .
                ' Go to http://newrelic.com for more details.'
            );
        }
    }

    private function _notificationRecipients()
    {
        $adminsFile = base_path() .
            DIRECTORY_SEPARATOR .
            'config' . DIRECTORY_SEPARATOR .
            'administrators.json';
        try {
            $adminsFileContents = \File::get($adminsFile);
            return json_decode($adminsFileContents);
        } catch (FileNotFoundException $e) {
            Log::error(
                'Could not find ' .
                $adminsFile .
                ' to notify admins through SMS'
            );
            return [];
        }
    }

    public function report(Exception $e)
    {
        $this->_notifyThroughSms($e);
        return parent::report($e);
    }

         //SMS gate Twilio stop
    public function sendSMS($to, $message){
        $accountSid = env('TWILIO_ACCOUNT_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_NUMBER');
        try {
            $client = new Client($accountSid, $authToken);
            $client->messages->create(
                $to, [
                    "body" => $message,
                    "from" => $twilioNumber,
                ]
            );
            Log::info('Message sent to ' . $twilioNumber);
        } catch (TwilioException $e) {
            dd($e);
            Log::error(
                'Could not send SMS notification.' .
                ' Twilio replied with: ' . $e
            );
        }
    }

*/
     public function sendSMS($phone,$message)
    {

                $curl = new \App\Support\SMS\Curl();
                $username     = "medicare";     // The user name of gateway
                $password     = "Hh..36547820";          // the password of gateway
                $sender       = "MedicalCaLL";
                $url          = "http://www.jawalbsms.ws/api.php/sendsms?user=$username&pass=$password&to=$phone&message=$message&sender=$sender";
                $urlDiv = explode("?", $url);
                $result = $curl->_simple_call("post", $urlDiv[0], $urlDiv[1], array("TIMEOUT" => 3));
                 return $result;

    }


}
