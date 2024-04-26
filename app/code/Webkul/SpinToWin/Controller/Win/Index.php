<?php
namespace Webkul\SpinToWin\Controller\Win;
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
        $spinObj = $this->dataHelper->getSpinBySpinId(0,2);
        $spinId = $spinObj->getId();
        //todo get user point and free count
       
        $data= ['spin_id' => $spinId,
            'start_date' => strtotime($spinObj->getStartDate()),
            'end_date' => strtotime($spinObj->getEndDate()),
            'rule' => $spinObj->getSpinRule(),
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
        
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;


    }

}
