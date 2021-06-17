<?php

namespace Magenmagic\PlaceOrderChecker\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;

class RemoteIp implements SectionSourceInterface
{

    public function getSectionData()
    {
        return [
            'ip'=> $_SERVER['REMOTE_ADDR']
        ];
    }
}
