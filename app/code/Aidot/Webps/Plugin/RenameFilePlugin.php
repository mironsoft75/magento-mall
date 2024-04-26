<?php
namespace Aidot\Webps\Plugin;
use Silk\Integrations\Utilities\ImageWebpUtil;
use Magento\Framework\Filesystem\Directory\WriteInterface;


class RenameFilePlugin{

    protected ImageWebpUtil $imageWebpUtil;
    public function __construct(
        ImageWebpUtil $imageWebpUtil
    )
    {
        $this->imageWebpUtil = $imageWebpUtil;
    }

    public function beforeRenameFile(\Magento\Framework\Filesystem\Directory\WriteInterface $subject,\Closure $proceed,$path, 
                $newPath, WriteInterface $targetDirectory = null){
        $result = $proceed($path,$newPath,$targetDirectory);
        if($path && !strstr($path,'tmp') && preg_match('/\.(png|jpe?g)$/i',$path)){
            $this->imageWebpUtil->changeFormat($_SERVER['DOCUMENT_ROOT'].'/media/'.$path);
        }
        
        return $path;
    }

}