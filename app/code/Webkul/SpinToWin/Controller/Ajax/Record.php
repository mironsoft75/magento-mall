<?php
namespace Webkul\SpinToWin\Controller\Ajax;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory as ReportsCollectionFactory;
use Silk\Integrations\Model\Session as UserSession;
class Record implements ActionInterface,HttpGetActionInterface{

    protected $ReportsCollectionFactory;
    protected $jsonFactory;
    protected $dataHelper;
    protected $_request;
    protected $userSession;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ReportsCollectionFactory $ReportsCollectionFactory,
        \Webkul\SpinToWin\Helper\Data $dataHelper,
        UserSession $userSession,
        JsonFactory $jsonFactory
    ){
        $this->_request = $context->getRequest();
        $this->ReportsCollectionFactory = $ReportsCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->userSession = $userSession;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute(){
        $list=[];
        $size = 20;
        if($this->userSession->isLoggedIn()){
            $page = $this->_request->getParam('page') ?? 1;
            $spinId = $this->_request->getParam('spin_id',null);
            
            $customerId = $this->userSession->getId();
            $collection = $this->ReportsCollectionFactory->create();
            $collection->addFieldToFilter('result',1)->addFieldToFilter('customer_id',$customerId);
            if(!empty($spinId)){
                $collection->addFieldToFilter('spin_id',$spinId);
            }
            $collection->getSelect()->joinLeft(['address' => $collection->getTable('spintowin_product_address')],
            'main_table.entity_id = address.reports_id',['address_id'])->limit($size,($page - 1)*$size);
            $collection->setOrder('main_table.timestamp','DESC');
            $list= [];
            $mediea = $this->dataHelper->getMediaDirectory();
            foreach($collection as $c){
                $list[]=[
                    'image' => $mediea.$c->getSegmentImage(),
                    'spin_type' => $c->getSpinType(),
                    'created_at' => strtotime($c->getTimestamp()),
                    'label' => $c->getSegmentLabel(),
                    'coupon_code' => $c->getCoupon(),
                    'reports_id' => $c->getId(),
                    'segment_url' => $c->getSegmentUrl(),
                    'address_id' => $c->getAddressId()
                ];
            }
        }
        $nextPage = !!($size == count($list));
        $back= ['code' => 200,'data' => ['list' => $list,'nextPage' => $nextPage]];
        $resultJson = $this->jsonFactory->create()->setData($back);
        return $resultJson;
    }

}