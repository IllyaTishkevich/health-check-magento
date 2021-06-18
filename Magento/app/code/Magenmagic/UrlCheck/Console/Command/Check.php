<?php

namespace Magenmagic\UrlCheck\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Check extends Command
{
    const NAME = 'option';
    
    protected $config;

    protected $loggerInterface;

    protected $resourceConnection;

    public function __construct(
        \Magenmagic\UrlCheck\Helper\Config $config,
        \Magenmagic\HealthCheck\Api\LoggerInterface $loggerInterface,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        string $name = null
        )
    {
        $this->resourceConnection = $resourceConnection;
        $this->loggerInterface = $loggerInterface;
        $this->config = $config;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('magenamagic:url:check');
        $this->setDescription('Get empty product url key or url path');
        $this->addOption(
            self::NAME,
            '-o',
            InputOption::VALUE_REQUIRED,
            'option'
        );

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $enabled = $this->config->isEnable();

        if(!$enabled)
            return true;
        
        $logLevel = $this->config->getLogLevel();
        $result = [];

        $productsUrlKey = $this->getFailProducts('url_key');
        
        foreach ($productsUrlKey as $product) {
            $id = $product['id'];
            $sku = $product['sku'];

            $result[$id] = 'url_key';
            $output->write("\n$id - $sku - url key");
        }

        
        if($input->getOption(self::NAME) === 'path'){
            $productsUrlPath = $this->getFailProducts('url_path');
            foreach ($productsUrlPath as $product) {
                $id = $product['id'];
                $sku = $product['sku'];

                if(isset($result[$id])){
                    $result[$id] = 'url_key,url_path';
                } else {
                    $result[$id] = 'url_path';
                }

                $output->write("\n$id - $sku - url path");
            }
        }

        if(empty($result)){
            $output->writeln("\n No empty URLs found!");
            return true;
        }

        try {
            $result = json_encode($result);
            $this->loggerInterface->log($logLevel, $result);
            $output->writeln("\nSend to HealthCheck successfully!");
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }

    }

    private function getFailProducts($attr)
    {
        $storeId = $this->config->getDefaultStoreId();
        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT entity_id as id, sku FROM catalog_product_entity WHERE entity_id NOT IN 
                (SELECT distinct entity_id FROM catalog_product_entity_varchar cev JOIN eav_attribute as eav 
                ON eav.attribute_id = cev.attribute_id WHERE eav.attribute_code = '$attr'
                AND cev.value != '' AND cev.store_id != $storeId)";
        $result = $connection->fetchAll($query);
        
        return $result;
    }
}
