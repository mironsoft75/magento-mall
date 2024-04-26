<?php

namespace Aidot\Webps\Helper\Rewrite;

use Magento\Catalog\Model\Product\Gallery\CreateHandler as GralleryCreateHandler;
use Silk\Integrations\Utilities\ImageWebpUtil;

class CreateHandler extends GralleryCreateHandler{

    protected ImageWebpUtil $imageWebpUtil;
    /**
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Silk\Integrations\Utilities\ImageWebpUtil
     * @param \Magento\Store\Model\StoreManagerInterface|null $storeManager
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Catalog\Model\ResourceModel\Product\Gallery $resourceModel,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        ImageWebpUtil $imageWebpUtil,
        \Magento\Store\Model\StoreManagerInterface $storeManager = null
    ) {
        parent::__construct($metadataPool,$attributeRepository,$resourceModel,$jsonHelper,$mediaConfig,$filesystem,$fileStorageDb,$storeManager);
        $this->imageWebpUtil = $imageWebpUtil;
    }
    

    /**
     * Move image from temporary directory to normal
     *
     * @param string $file
     * @return string
     * @since 101.0.0
     */
    protected function moveImageFromTmp($file)
    {
        $file = $this->getFilenameFromTmp($this->getSafeFilename($file));
        $destinationFile = $this->getUniqueFileName($file);

        if ($this->fileStorageDb->checkDbUsage()) {
            $this->fileStorageDb->renameFile(
                $this->mediaConfig->getTmpMediaShortUrl($file),
                $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->mediaConfig->getMediaPath($destinationFile));
        } else {
            $oldPath = $this->mediaConfig->getTmpMediaPath($file);
            $newPath = $this->mediaConfig->getMediaPath($destinationFile);
            $this->mediaDirectory->renameFile(
                $oldPath,$newPath
            );
            $this->imageWebpUtil->changeFormat($_SERVER['DOCUMENT_ROOT'].'/media/'.$newPath);
        }

        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * Returns safe filename for posted image
     *
     * @param string $file
     * @return string
     */
    private function getSafeFilename($file)
    {
        $file = DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);

        return $this->mediaDirectory->getDriver()->getRealPathSafety($file);
    }
}