<?php


namespace Magenmagic\HealthCheck\Model;


class Auth
{
    const API = 'api/signin/';

    protected $config;

    protected $logger;

    public function __construct(
        \Magenmagic\HealthCheck\Helper\Config $config,
        \Magenmagic\HealthCheck\Logger\Logger $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    public function getToken()
    {
        $url = $this->config->getUrl() . '/'. self::API
            . $this->config->getLogin() . '/' . $this->config->getPassword()
            . '/' . $this->config->getKey();

        $content = file_get_contents($url);
        try {
            $json = json_decode($content);
            if (isset($json->error)) {
                $this->logger->error($json->error);
                return ;
            }
            return $json->token;
        } catch (\Exception $e) {
            $this->logger->info($content);
            $this->logger->error($e->getMessage());
        }

    }
}
