<?php


namespace Magenmagic\HealthCheck\Model;


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
        $url = $this->config->getUrl();
        $remoteAddr = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        //The data you want to send via POST
        $fields = [
            'level' => $this->level,
            'data' => $this->body,
            'ip' => $this->ip === null ? $remoteAddr : $this->ip
        ];

        //url-ify the data for the POST
        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authentication-Key: ' . $this->config->getKey()
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);

        //execute post
        $result = curl_exec($ch);
        //echo $result;

        if ($result === false) {
            throw new \Exception(curl_error($ch), curl_errno($ch));
        }

        return $result;
    }
}
