<?php
namespace Aidot\Webps\Plugin;
use Aidot\Webps\Utilities\ImageWebpUtil;


class MoveFileFromTmpPlugin{

    protected ImageWebpUtil $imageWebpUtil;
    public function __construct(
        ImageWebpUtil $imageWebpUtil
    )
    {
        $this->imageWebpUtil = $imageWebpUtil;
    }

    public function afterMoveFileFromTmp(\Magento\Catalog\Model\ImageUploader $subject,$path){
        
        if($path && !strstr($path,'tmp') && preg_match('/\.(png|jpe?g)$/i',$path)){
            $this->imageWebpUtil->changeFormat($_SERVER['DOCUMENT_ROOT'].'/media/'.$path);
        }
        
        return $path;
    }

}