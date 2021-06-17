<?php
/**
 *
 *  * @author MagenMagic Team
 *  * @copyright Copyright (c) 2021 MagenMagic (https://www.magenmagic.com)
 *  * @package
 *
 */

namespace Magenmagic\InventoryCheck\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

class TxtFile extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TXT_FILE_NAME_TEMPLATE = 'export/mmcheckinventory/';

    private $filesystem;

    protected $dir;

    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\DirectoryList $dir
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

    public function createTxtFileAndWriteToIt($contents, $fileName = "filename")
    {
        $csvFileName = $this->createTxtFileName($fileName);

        $directory = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $stream    = $directory->openFile($csvFileName, 'w+');
        try {
            $stream->lock();
            try {
                $stream->write($contents);
            } finally {
                $stream->unlock();
            }
        } finally {
            $stream->close();
        }

        return $csvFileName;
    }

    private function createTxtFileName($fileName)
    {
        $result = self::TXT_FILE_NAME_TEMPLATE . $fileName . date('m_d_Y_H_i_s') . '.txt';

        return $result;
    }
}
