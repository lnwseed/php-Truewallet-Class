<?php
/**
* TrueWallet Class
 *
 * @category  Payment Gateway
 * @package   php-truewallet-class
 * @copyright Copyright (c) 2021-2022
 * @link      https://github.com/lnwseed/ishare
 * @version   1.1.0
 *
**/

class iwallet
{
    public $access_token = null;
    public $curl_options = array(
        CURLOPT_SSL_VERIFYPEER => false
    ); // ceolnw
    public $api_gateway = "https://3ird.online/donate/";
    public function __construct()
    {
    }
    public function request($api_path, $headers = array() , $data = null)
    {
        $this->data = null;
        $handle = curl_init($this->api_gateway . ltrim($api_path, "/"));
        if (!is_null($data))
        {
            curl_setopt_array($handle, array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => is_array($data) ? json_encode($data) : $data
            ));
            if (is_array($data)) $headers = array_merge(array(
                "Content-Type" => "application/json"
            ) , $headers);
        }
        curl_setopt_array($handle, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => "okhttp/3.8.0",
            CURLOPT_HTTPHEADER => $this->buildHeaders($headers)
        ));
        if (is_array($this->curl_options)) curl_setopt_array($handle, $this->curl_options);
        $this->response = curl_exec($handle);
        $this->http_code = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if ($result = json_decode($this->response, true))
        {
            if (isset($result["data"])) $this->data = $result["data"];
            return $result;
        }
        return $this->response;
    }
    public function buildHeaders($array)
    {
        $headers = array();
        foreach ($array as $key => $value)
        {
            $headers[] = $key . ": " . $value;
        }
        return $headers;
    }
    public function RequestLoginOTP($mobile_number = null, $password = null)
    {
        if (!isset($mobile_number) || !isset($password))
        {
            return false;
        }
		return $this->request("/RequestLoginOTP/" . $mobile_number . "/" . $password);
    }
    public function SubmitLoginOTP($otp_code, $mobile_number = null, $otp_reference = null)
    {
        if (!isset($otp_code) || !isset($mobile_number) || !isset($otp_reference))
        {
            return false;
        }
        return $this->request("/SubmitLoginOTP/" . $otp_code . "/" . $mobile_number . "/" . $otp_reference);
    }
    public function GetProfile()
    {
        if (is_null($this->access_token)) return false;
        return $this->request("/GetProfile/" . $access_token);
    }
    public function GetBalance()
    {
        if (is_null($this->access_token)) return false;
        return $this->request("/GetBalance/" . $access_token);
    }
    public function setAccessToken($access_token)
    {
        $this->access_token = is_null($access_token) ? null : strval($access_token);
    }
    public function Logout()
    {
        if (is_null($this->access_token)) return false;
        return $this->request("/Logout/" . $access_token);
    }
    public function GetTransaction($limit = 50, $tmn = null, $start_date = null, $end_date = null)
    {
        if (is_null($this->access_token)) return false;
        if (is_null($start_date) && is_null($end_date)) $start_date = date("Y-m-d", strtotime("-30 days") - date("Z") + 25200);
        if (is_null($end_date)) $end_date = date("Y-m-d", strtotime("+1 day") - date("Z") + 25200);
        if (is_null($start_date) || is_null($end_date)) return false;
        return $this->request("/GetTransaction/" . $access_token . "/" . $limit . "/" . $tmn . "/" . $start_date . "/" . $end_date);
    }

}

?>
