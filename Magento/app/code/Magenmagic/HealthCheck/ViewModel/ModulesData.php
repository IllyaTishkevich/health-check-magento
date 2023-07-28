<?php


namespace Magenmagic\HealthCheck\ViewModel;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Module\ModuleList;

class ModulesData implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var FullModuleList
     */
    protected $fullModuleList;

    /**
     * @var ModuleList
     */
    protected $moduleList;

    public function __construct(
        FullModuleList $fullModuleList,
        ModuleList $moduleList
    ) {
        $this->fullModuleList = $fullModuleList;
        $this->moduleList = $moduleList;
    }

    /**
     * Returns enabled modules
     *
     * @return array
     */
    public function getEnabledModules(): array
    {
        $modules = [];
        foreach ($this->moduleList->getAll() as $module) {
            $modules[] = $module['name'];
        }

        return $modules;
    }

    /**
     * Returns disabled modules
     *
     * @return array
     */
    public function getDisabledModules(): array
    {
        $enabledModules = $this->getEnabledModules();
        $modules = [];
        foreach ($this->fullModuleList->getAll() as $module) {
            if (!in_array($module['name'], $enabledModules)) {
                $modules[] = $module['name'];
            }
        }

        return $modules;
    }
}
