<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SpinToWin\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;

class MoveDirToMediaDir implements DataPatchInterface
{
    /**
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;

    /**
     * Constructor
     *
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     */
    public function __construct(
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->logger = $logger;
    }
    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        try {
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            $reader = $objManager->get(\Magento\Framework\Module\Dir\Reader::class);
            $filesystem = $objManager->get(\Magento\Framework\Filesystem::class);
            $fileDriver = $objManager->get(\Magento\Framework\Filesystem\Driver\File::class);

            $type = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
            $smpleFilePath = $filesystem->getDirectoryRead($type)
                                        ->getAbsolutePath().'spintowin/image/';
            $files = [
                'red.png',
                'green.png',
                'blue.png',
                'purple.png',
                'yellow.png',
                'wheel.png',
                'red_pin.png',
                'green_pin.png',
                'yellow_pin.png',
                'purple_pin.png',
                'coupon.png'
            ];
            if ($fileDriver->isExists($smpleFilePath)) {
                $fileDriver->deleteDirectory($smpleFilePath);
            }
            if (!$fileDriver->isExists($smpleFilePath)) {
                $fileDriver->createDirectory($smpleFilePath, 0777);
            }
            foreach ($files as $file) {
                $filePath = $smpleFilePath.$file;
                if (!$fileDriver->isExists($filePath)) {
                    $path = '/view/base/web/image/'.$file;
                    $mediaFile = $reader->getModuleDir('', 'Webkul_SpinToWin').$path;
                    if ($fileDriver->isExists($mediaFile)) {
                        $fileDriver->copy($mediaFile, $filePath);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info("Move Dir To Media Dir ERROR :  ".$e->getMessage());
        }
    }

    /**
     * Get Aliases
     *
     * @return array
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * Get Dependencies
     *
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }
}
