<?php


namespace Magenmagic\HealthCheck\Controller\Index;


use Magento\Framework\App\ResponseInterface;

class Test extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $ob = \Magento\Framework\App\ObjectManager::getInstance();
        $a = $ob->create('\Magenmagic\HealthCheck\Api\LoggerInterface');

        $a->log('12', '4432', '127.0.0.1');
    }
}