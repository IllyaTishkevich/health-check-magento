<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2020 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class CsvFile extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CSV_FILE_NAME_TEMPLATE = 'export/mmcheckinventory/';

    private $filesystem;

    protected $dir;

    public function __construct(
        Context $context, \Magento\Framework\Filesystem $filesystem, \Magento\Framework\Filesystem\DirectoryList $dir
    ) {
        $this->filesystem = $filesystem;
        $this->dir        = $dir;
        parent::__construct($context);
    }

    public function generateCsvArray($items)
    {
        $result[] = $this->generateCsvTitleLine();
        foreach ($items as $item) {

            $result[] = $this->generateCsvInfoLine($item);
        }

        return $result;
    }

    public function createCsvFileAndWriteToIt($dataArray, $fileName = "filename")
    {
        $csvFileName = $this->createCsvFileName($fileName);

        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $stream    = $directory->openFile($csvFileName, 'w+');
        $stream->lock();
        foreach ($dataArray as $nextLineArray) {
            $stream->writeCsv($nextLineArray);
        }
        $stream->flush();
        $stream->unlock();

        return $csvFileName;
    }

    private function createCsvFileName($fileName)
    {
        $result = self::CSV_FILE_NAME_TEMPLATE . $fileName . date('m_d_Y_H_i_s') . '.csv';

        return $result;
    }

    private function generateCsvTitleLine()
    {
        $result   = [];
        $result[] = 'Product Ids';

        return $result;
    }

    private function generateCsvInfoLine($item)
    {
        $result   = [];
        $result[] = isset($item['product_id']) ? $item['product_id'] : $item;

        return $result;
    }
}
