<?php
namespace Webkul\SpinToWin\Controller\Ajax;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Silk\Integrations\Model\Session as UserSession;
use Silk\Integrations\Model\UserLogin;

class Index implements ActionInterface,HttpGetActionInterface{
    protected $_request;
    protected $jsonFactory;
    protected $dataHelper;
    protected $userSession;
    protected $userLogin;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResultFactory $jsonFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        UserSession $userSession,
        UserLogin $userLogin
    ){
        $this->_request = $context->getRequest();
        $this->jsonFactory = $jsonFactory;
        $this->dataHelper = $helper;
        $this->userSession = $userSession;
        $this->userLogin = $userLogin;
    }

    public function execute(){
        $back= ['code' => 200];
        if($this->userSession->isLoggedIn()){
            $spinId = $this->_request->getParam('spin_id');
            $spinObj = $this->dataHelper->getSpinBySpinId($spinId);
            $spinId = $spinObj->getId();
            //todo get user point and free count
            $customerId = $this->userSession->getId();
            $freeCount = $this->dataHelper->getTodayTime($customerId,$spinId);
            $freeNum = $spinObj->getFreeNum();
            $freeNum = $freeNum > $freeCount ? $freeNum-$freeCount : 0;
            $point = $this->userLogin->getPointsByUid($this->userSession->getAppUserId());
            $data= ['point' => $point,'free_num' => $freeNum,'spin_id' => $spinId,
                'rule' => $spinObj->getSpinRule(),
                'redeem_point' => $spinObj->getPoint(),
                'segments' => []
            ];
            $mediea = $this->dataHelper->getMediaDirectory();
            foreach($spinObj->getSegments() as $segment){
                $data['segments'][]= [
                    'label' => $segment->getLabel(),
                    'type' => $segment->getType(),
                    'spin_type' => $segment->getSpinType(),
                    'segment_id' => $segment->getEntityId(),
                    'image' => $mediea.$segment->getImage()
                ];
            }
            $back['data']= $data;
        }else {
            $back['code'] = 1000;
            $back['msg'] = __("Please Login");
        }
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;


    }

}
