<?php


namespace Magenmagic\HealthCheck\Model;

use tests\unit\Magento\FunctionalTestFramework\Util\Validation\DuplicateNodeValidationUtilTest;

class Transaction
{
    protected $config;

    private $body;

    private $ip;

    private $level;

    public function __construct(
        \Magenmagic\HealthCheck\Helper\Config $config
    ) {
        $this->config = $config;
    }

    public function setBody(string $text)
    {
        $this->body = $text;
    }

    public function setIp(string $ip = null)
    {
        $this->ip = $ip;
    }

    public function setLevel(string $level)
    {
        $this->level = $level;
    }

    public function getResponce()
    {
        $ip = $this->ip === null
            ? isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined'
            : $this->ip;

        $url = $this->config->getUrl();
        //The data you want to send via POST
        $fields = [
            'level' => $this->level,
            'data' => $this->body,
            'ip' => $ip
        ];

        //url-ify the data for the POST
        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_TIMEOUT_MS, /*$this->config->getTimeoutValue()*/ 10000);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authentication-Key: ' . $this->config->getKey()
        ));

        //execute post
        $result = curl_exec($ch);

        return $result;
    }

    public function send()
    {
        $resolver = function () {
            $ip = $this->ip === null
                ? isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'undefined'
                : $this->ip;

            $url = $this->config->getUrl();
            //The data you want to send via POST
            $fields = [
                'level' => $this->level,
                'data' => $this->body,
                'ip' => $ip
            ];

            //url-ify the data for the POST
            $fields_string = http_build_query($fields);

            //open connection
            $ch = curl_init();

            //set the url, number of POST vars, POST data
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->config->getTimeoutValue());
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authentication-Key: ' . $this->config->getKey()
            ));

            //execute post
            $result = null;
            try {
                $result = curl_exec($ch);
            } catch (\Exception $e) {
                
            }

//            return $result;
        };

        $canceller = function () {
            throw new Exception('Promise errored');
        };

        $promise = new \React\Promise\Promise($resolver, $canceller);
        $promise->then();
    }
}
