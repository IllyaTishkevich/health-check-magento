<?php


namespace Magenmagic\GapsChecker\Helper;


use \Magento\Store\Model\ScopeInterface;

class Config
{
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnable()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/enable', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getDayPeriod()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/day', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getNightPeriod()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/night', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getPeriodForEmail()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/sendmail/list_mode', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getEnableGapsStatic()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/sendmail/enabled_gaps_static', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLog()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/log_level', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getLogStatic()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/log_level_static', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getTestValue()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/test', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getTimeZone()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/time_zone', ScopeInterface::SCOPE_WEBSITE);
    }


    public function getPeriod()
    {
        $dt = new \DateTime();
        $dt->setTimezone(new \DateTimeZone($this->getTimeZone()));
        $hour = $dt->format('H');
        
        if($hour < 23 && $hour >= 7) {
            return $this->getDayPeriod();
        } else {
            return $this->getNightPeriod();
        }
    }

    public function getSalesChannelsString() {
        $channels = $this->getSalesChannels();
        $arr = explode(',',$channels);
        $str =  "'".implode("','", $arr)."'";

        return $str;
    }

    public function getPeriodByTime($date)
    {
        $hour = new \DateTime($date);
        $hour->setTimezone(new \DateTimeZone($this->getTimeZone()));
        $formatHour = $hour->format('H');

        if($formatHour < 23 && $formatHour >= 7) {
            return $this->getDayPeriod();
        } else {
            return $this->getNightPeriod();
        }
    }


    public function getSalesChannels()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/channel', ScopeInterface::SCOPE_WEBSITE);
    }

    public function getEnableSalesChannels()
    {
        return $this->scopeConfig->getValue('mm_health_check/setting/gaps/enable_sales_channel', ScopeInterface::SCOPE_WEBSITE);
    }

    public function isEmailEnable() {
        return $this->scopeConfig->getValue(
            'mm_health_check/setting/sendmail/enabled',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getEmailTemplate() {
        return $this->scopeConfig->getValue(
            'mm_health_check/setting/sendmail/email_template',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getEmailTemplateStatic() {
        return $this->scopeConfig->getValue(
            'mm_health_check/setting/sendmail/email_template_static',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getEmailSender()
    {
        return $this->scopeConfig->getValue(
            'mm_health_check/setting/sendmail/sender',
            ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getEmailRecipient()
    {
        return $this->scopeConfig->getValue(
            'mm_health_check/setting/sendmail/recipients',
            ScopeInterface::SCOPE_WEBSITE
        );
    }
}
