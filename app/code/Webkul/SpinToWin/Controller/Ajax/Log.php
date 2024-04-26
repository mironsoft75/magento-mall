<?php
namespace Webkul\SpinToWin\Controller\Ajax;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory as ReportsCollectionFactory;
class Log implements ActionInterface,HttpGetActionInterface{

    protected $_request;
    protected $ReportsCollectionFactory;
    protected $jsonFactory;
    protected $dataHelper;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ReportsCollectionFactory $ReportsCollectionFactory,
        \Webkul\SpinToWin\Helper\Data $dataHelper,
        ResultFactory $jsonFactory
    ){
        $this->_request = $context->getRequest();
        $this->ReportsCollectionFactory = $ReportsCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute(){
        $spinId = $this->_request->getParam('spin_id');
        $size = 20;
        $collection = $this->ReportsCollectionFactory->create()->addFieldToFilter('spin_id',$spinId)
            ->addFieldToFilter('result',1)->setOrder('timestamp','DESC')->setPageSize($size);
        $list= [];
        $mediea = $this->dataHelper->getMediaDirectory();
        foreach($collection as $c){
            $list[]=[
                'image' => $mediea.$c->getSegmentImage(),
                'content' => __("Congratulations! %1 has won %2",$this->dataHelper->ceshi($c->getName()),$c->getSegmentLabel())
            ];
        }
        if($size > count($list) && count($list)){
            $list = $this->arrayPad($list,$list,$size);
        }
        $back= ['code' => 200,'data' => ['list' => $list]];
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;
    }

    private function arrayPad($list,$oldList,$size){
        $list = array_merge($list,$oldList);
        if(count($list) >=$size){
            return array_slice($list,0,$size,true);
        }
        return $this->arrayPad($list,$oldList,$size);
    }

}
