<?php

namespace Aidot\Webps\Model;

use Aidot\Webps\Api\ImageManagerInterface;
use Aidot\Webps\Model\ResourceModel\ImageWebpsLogFactory;
use Aidot\Webps\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\Webapi\Rest\Request;
use Silk\Integrations\Utilities\ImageWebpUtil;

class ImageManager implements ImageManagerInterface {

    protected ImageWebpsLogFactory $imagewebpsLogFactory;
    protected CollectionFactory $collectionFactory;
    protected ImageWebpUtil $imageWebpUtil;
    protected Request $request;
    public function __construct(
        ImageWebpsLogFactory $imagewebpsLogFactory,
        CollectionFactory $collectionFactory,
        ImageWebpUtil $imageWebpUtil,
        Request $request
    )
    {
        $this->imagewebpsLogFactory = $imagewebpsLogFactory;
        $this->collectionFactory = $collectionFactory;
        $this->imageWebpUtil = $imageWebpUtil;
        $this->request = $request;
    }

    public function manager()
    {
        $root = $_SERVER['DOCUMENT_ROOT'].'/media';
        $fileArr = $this->getDir($root);
        $isFilter = $this->request->getParam('filter',0);
        $list= [];
        $connection = $this->imagewebpsLogFactory->create()->getConnection();
        foreach($fileArr as $fileInfo){
            $name = $fileInfo['file_name'];
            if(preg_match('/.(jpg|jpeg|png)/',$name)){
                $filePath = $fileInfo['path'];
                if($isFilter){
                    $firstItem = $this->collectionFactory->create()->addFieldToFilter('image',$name)->addFieldToFilter('image_path',$filePath)->getFirstItem();
                    if($firstItem->hasData()){
                        continue;
                    }
                }
                $list[]= [
                    'image' => $name,
                    'image_path' => $filePath
                ];
                if(count($list) === 5000){
                    $connection->insertMultiple($connection->getTableName('image_webps_log'),$list);
                    $list= [];
                }
            }
        }
        if(count($list)){
            $connection->insertMultiple($connection->getTableName('image_webps_log'),$list);
        }
        return 'success';
    }

    public function execute()
    {
        $param = $this->request->getParams();
        $page = $param['page'];
        $size = $param['size'];
        $collection = $this->collectionFactory->create()->setCurPage($page)->setPageSize($size);
        $result = $collection->getItems();
        
        foreach($result as $imageInfo){
            if($imageInfo->getStatus()){
                continue;
            }
            $path = $imageInfo->getImagePath();
            $name = $imageInfo->getImage();
            $this->imageWebpUtil->changeFormat($path);
            $imageInfo->setStatus(1)->save();

        }
        return 'success';
    }

    /**
     * 修复个别图片
     */
    public function repair()
    {
        // $param = $this->request->getParams();
        // $path =  urldecode($param['url']);
        // $this->imageWebpUtil->changeFormat($path);
        $connection = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class)->getConnection();
        $table = 'sales_order_grid';
        $result = $connection->fetchAll("select entity_id from $table where product_name = '' and status = 'processing' and  entity_id >=7151");//7175
        if(count($result)){
            foreach($result as $row){
                $orderId= $row['entity_id'];
                $items = $connection->fetchAll("select sku,qty_ordered,name from sales_order_item where order_id = $orderId and parent_item_id is null");
                $productQty= [];
                $productName= [];
                if(count($items)){
                    $skuStr = '';
                    foreach($items as $item){
                        $skuStr .= (empty($skuStr) ? $item['sku'] :';'.$item['sku']);
                        $productQty[]=$item['qty_ordered'];
                        $productName[]= $item['name'];
                    }
                    if(!empty($skuStr)){
                        $connection->update($table,
                        ['product_sku' => $skuStr,'product_qty' =>implode(';',$productQty),'product_name' => implode(';',$productName),'repurchase' => 'no'],
                        "entity_id=$orderId");
                    }
                }
            }
        }
        return 'success';
    }

    // private function searchDir($path,&$files,$fileName){

    //     if(is_dir($path)){
    //         \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->error('patth    '. $path);
    //         $opendir = opendir($path);
    
    //         while ($file = readdir($opendir)){
    //             \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class)->error('    '.$file);
    //             if($file != '.' && $file != '..' && !strstr($file,'cache')){
    //                 $this->searchDir($path.'/'.$file, $files,$file);
    //             }
    //         }
    //         closedir($opendir);
    //     }
    //     if(!is_dir($path)){
    //         $files[] = ['path' =>$path,'file_name' => $fileName];
    //     }
    // }

    private function searchDir($path,&$files,$fileName){

        if(is_dir($path)){
            $opendir = scandir($path);
            foreach($opendir as $file){
                if($file != '.' && $file != '..' && !strstr($file,'cache')){
                    $this->searchDir($path.'/'.$file, $files,$file);
                }
            }
        }
        if(!is_dir($path)){
            $files[] = ['path' =>$path,'file_name' => $fileName];
        }
    }
    //得到目录名
    private function getDir($dir){
        $files = array();
        $this->searchDir($dir, $files,'');
        return $files;
    }
}