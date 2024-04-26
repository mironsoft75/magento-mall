<?php
namespace Aidot\Webps\Plugin;
use Aidot\Webps\Utilities\ImageWebpUtil;
use Aidot\Webps\Model\ResourceModel\Account\CollectionFactory as AccountCollectionFactory;

class UploaderPlugin{

    protected ImageWebpUtil $imageWebpUtil;
    protected AccountCollectionFactory $accountCollectionFactory;
    public function __construct(
        ImageWebpUtil $imageWebpUtil,
        AccountCollectionFactory $accountCollectionFactory
    )
    {
        $this->imageWebpUtil = $imageWebpUtil;
        $this->accountCollectionFactory = $accountCollectionFactory;
    }

    public function beforeSave(\Magento\Framework\File\Uploader $subject,$destinationFolder, $newFileName = null){
        if(empty($newFileName)){
            $val = (array_values($_FILES));
            if(count($val)){
                $imageInfo = $val[0];
                if(preg_match('/\.(png|jpe?g)$/i',$imageInfo['name'])){
                    $typeArr = explode('.',$imageInfo['name']);
                    $newFileName = md5_file($imageInfo['tmp_name']).'.'.$typeArr[count($typeArr)-1];
                }
            }
        }
        return [$destinationFolder,$newFileName];
    }

    public function afterSave(\Magento\Framework\File\Uploader $subject,$result){
        $path = $result['path'].'/'.$result['file'];
        if(strstr($path,'tmp')){
            $this->compress($path);
            return $result;
        }
        if(strstr($path,'media/wysiwyg')){
            $this->compress($path);
        }
        $this->imageWebpUtil->changeFormat($path);
        return $result;
    }

    private function compress($path){
        if(preg_match('/\.(png|jpe?g)$/i',$path)){
            $account = $this->accountCollectionFactory->create()->addFieldToFilter('enabled',1)->setOrder('free_num','desc')->getFirstItem();
            if($account->hasData() && $account->getFreeNum() > 0){
                $this->imageWebpUtil->compressImage($account->getAppKey(),$path);//"XDzDK23bl4V0wJ0774RCQ8c8xhX9zW9J"
                $account->setFreeNum($account->getFreeNum()-1)->save();
            }
            
        }
    }

}