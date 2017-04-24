<?php

namespace Riazxrazor\Slybroadcast;

use GuzzleHttp\Client;

class Slybroadcast
{
    const BASE_URI = 'https://www.mobile-sphere.com/gateway/';

    public $DEBUG = false;
    // GuzzleHttp client for making http requests
    /**
     * Guzzle response
     */
    public $responseData;
    public $rawResponse;

    protected $httpClient;

    public $username;
    public $password;

    public function __construct($uid,$pass)
    {
        $this->setCredentials($uid,$pass);
        $this->httpClient = new Client([
            'base_uri' => self::BASE_URI,
            'timeout' => 10
        ]);
    }

    public function setCredentials($uid,$pass)
    {
        $this->username = $uid;
        $this->password = $pass;
        return $this;
    }

    public function sendVoiceMail($postdata)
    {
        $postdata['c_uid'] = $this->username;
        $postdata['c_password'] = $this->password;
        $this->apiCall('POST','vmb.php',$postdata);
        return $this;
    }

    public function pause($session_id)
    {
        $postdata['c_option'] = 'pause';
        $postdata['session_id'] = $session_id;
        $postdata['c_uid'] = $this->username;
        $postdata['c_password'] = $this->password;

        $this->apiCall('POST','vmb.php',$postdata);
        return $this;
    }

    public function resume($session_id)
    {
        $postdata['c_option'] = 'run';
        $postdata['session_id'] = $session_id;
        $postdata['c_uid'] = $this->username;
        $postdata['c_password'] = $this->password;

        $this->apiCall('POST','vmb.php',$postdata);
        return $this;
    }

    public function accountMessageBalance()
    {
        $postdata['remain_message'] = '1';
        $postdata['c_uid'] = $this->username;
        $postdata['c_password'] = $this->password;
        $this->apiCall('POST','vmb.php',$postdata);
        return $this;
    }



    public function listAudioFiles()
    {
        $postdata['c_method'] = 'get_audio_list';
        $postdata['c_uid'] = $this->username;
        $postdata['c_password'] = $this->password;

        $this->apiCall('POST','vmb.aflist.php',$postdata);
        $this->parseAudioResponse();
        return $this;
    }

    public function getResponse()
    {
        return $this->responseData;
    }

    public function getRawResponse()
    {
        return $this->rawResponse;
    }


    private function apiCall(string $method, string $url,$postdata = [],array $extraData = [])
    {

        $params = [
        ];

        // POST REQUEST
        if(!empty($postdata))
        {
            $postdata['c_uid'] = $this->username;
            $postdata['c_password'] = $this->password;
            $params['form_params'] = $postdata;

            $params['headers'] = [
                'User-Agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36',
                'Upgrade-Insecure-Requests' => 1,
                'Content-Type' =>  'application/x-www-form-urlencoded',
                'Accept' =>  'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'en-US,en;q=0.8',
                'Cache-Control' => 'max-age=0',
                'Connection' => 'keep-alive',
            ];

            $params['debug'] = $this->DEBUG;

            $response = $this->httpClient->request($method, $url, $params);

        }
        else
        {
            //GET REQUEST
            $extraData = array_merge($extraData,$params);

            $extraData['debug'] = $this->DEBUG;

            $response = $this->httpClient->request($method, $url, $extraData);

        }


        $this->rawResponse = $response;
        $this->responseData = $response->getBody()->getContents();
        $this->parseResponse();
    }

    private function parseAudioResponse()
    {
        //$this->responseData = explode("\n",$this->responseData);
        $tmp = $this->responseData;
        $arr = [];
        if(!empty($this->responseData))
        {
            foreach ($this->responseData as $key => $value)
            {
                if(strpos($value,'|') != FALSE)
                {
                    $t = explode('|',$value);
                    $arr[] = $t;
                }
            }
            if(!empty($arr))
            {
                $this->responseData = $arr;
            }

        }

    }

    private function parseResponse()
    {
        $this->responseData = explode("\n",$this->responseData);
        $tmp = $this->responseData;
        $arr = [];
        if(!empty($this->responseData))
        {
            foreach ($this->responseData as $key => $value)
            {
                if(strpos($value,'=') != FALSE)
                {
                    $t = explode('=',$value);
                    $arr[$t[0]] = $t[1];
                }
            }
            if(!empty($arr))
            {
                $this->responseData = $arr;
            }

        }

    }
}