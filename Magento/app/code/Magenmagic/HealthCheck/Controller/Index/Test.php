<?php


namespace Magenmagic\HealthCheck\Controller\Index;


use Magento\Framework\App\ResponseInterface;

class Test extends \Magento\Framework\App\Action\Action
{

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if(isset($params['level']) && isset($params['message'])) {
            $ob = \Magento\Framework\App\ObjectManager::getInstance();
            $a = $ob->create('\Magenmagic\HealthCheck\Api\LoggerInterface');

            $a->log($params['level'],$params['message'], isset($params['ip']) ? $params['ip'] : null );
        } else {
            print_r('no data');
        }
    }
}