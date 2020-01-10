<?php
namespace App\Support\SMS;
/**
* Send SMS Messages and Check Balance.
*/
class SMS
{
    public function __construct(Curl $curl, $baseUrl)
    {
        $this->curl = $curl;
        $this->baseUrl = $baseUrl;
    }

    protected $curl;
    protected $baseUrl;
    protected $message;
    protected $sender;
    protected $username;
    protected $password;
    protected $phones = [];

    public function send()
    {
        $url = $this->buildEndPoint('sendsms', [
            'user' => $this->username(),
            'pass' => $this->password(),
            'to' => $this->phones(),
            'message' => $this->message(),
            'sender' => $this->sender(),
        ]);

        $urlDiv = explode("?", $url);

        return $this->curl->_simple_call("post", $urlDiv[0], $urlDiv[1], array("TIMEOUT" => 3));
    }

    public function balance()
    {
        $url = $this->buildEndPoint('chk_balance', [
            'user' => $this->username(),
            'pass' => $this->password(),
        ]);

        $urlDiv = explode("?", $url);

        return $this->curl->_simple_call("post", $urlDiv[0], $urlDiv[1], array("TIMEOUT" => 3));
    }

    protected function buildEndPoint($type , $params)
    {
        return "$this->baseUrl/$type?" . http_build_query($params, '', '&');
    }

    public function __call($method, $args = [])
    {
        switch ($method) {
            case 'message':
            case 'sender':
            case 'username':
            case 'password':
            case 'phones':
            case 'curl':
            case 'baseUrl':
            if (isset($args[0]) && !empty($args[0])) {
                $this->$method = is_array($args[0])? implode(',', $args[0]) : $args[0];
                return $this;
            }
            return $this->$method;
            default:
            if (method_exists($this , $method)) {
                return call_user_func_array([$this, $method], $args);
            }
            throw new \Exception('The method you are looking for is not found.');
        }
    }


}
