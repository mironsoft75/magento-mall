<?php
namespace Webkul\SpinToWin\Controller\Win;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Silk\Integrations\Model\Session as UserSession;
use Webkul\SpinToWin\Model\ResourceModel\ProductAddressFactory;
use Webkul\SpinToWin\Model\ProductAddressFactory as ProductAddressModelFactory;
use Webkul\SpinToWin\Model\ResourceModel\Product\CollectionFactory as AddressProdcutCollectionFactory;
use Webkul\SpinToWin\Helper\ProductEmail;
class Address implements ActionInterface,HttpPostActionInterface{

    protected $jsonFactory;
    protected $dataHelper;
    protected $_request;
    protected $userSession;
    protected $productAddressFactory;
    protected AddressProdcutCollectionFactory $addressProductCollectionFactory;
    protected $productEmail;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Webkul\SpinToWin\Helper\Data $dataHelper,
        UserSession $userSession,
        ResultFactory $jsonFactory,
        ProductAddressFactory $productAddressFactory,
        ProductAddressModelFactory $productAddressModelFactory,
        AddressProdcutCollectionFactory $addressProductCollectionFactory,
        ProductEmail $productEmail
    ){
        $this->_request = $context->getRequest();
        $this->dataHelper = $dataHelper;
        $this->userSession = $userSession;
        $this->jsonFactory = $jsonFactory;
        $this->productAddressFactory = $productAddressFactory;
        $this->productAddressModelFactory = $productAddressModelFactory;
        $this->addressProductCollectionFactory = $addressProductCollectionFactory;
        $this->productEmail = $productEmail;
    }

    public function execute(){

        $addressData = $this->_request->getParams();
        $reportId = $addressData['reports_id'];
        $address= [
            'reports_id' => $reportId,
            'email' => $addressData['email'],
            'firstname' => $addressData['firstname'],
            'lastname' => $addressData['lastname'],
            'street' => empty($addressData['street']) ? '' :$addressData['street'][0],
            'country_id' => $addressData['country_id'],
            'city' => $addressData['city'],
            'region' => $addressData['region'],
            'region_id' => $addressData['region_id'],
            'postcode' => $addressData['postcode'],
            'telephone' => $addressData['telephone']
        ];
        $back= ['code' => 200,'msg' => __("Prize(s) will be sent to you ASAP! Please be patience")];
        try{
            if(!empty($addressData['address_id'])){
                $address['address_id'] = $addressData['address_id'];
            }else{
                $obj= $this->addressProductCollectionFactory->create()->addFieldToFilter('reports_id',$reportId)->getFirstItem();
                if($obj->hasData()){
                    // throw new Exception(__("Your Prize has been charge"));
                    $address['address_id'] = $obj->getAddressId();
                }
            }
            $model = $this->productAddressModelFactory->create();
            $model->setData($address);
            $resourceModel = $this->productAddressFactory->create();
            $resourceModel->save($model);
            //Send Email
            $this->productEmail->sendNotification($address['email']);
        }catch(Exception $e){
            $back['code'] = 201;
            $back['msg'] = $e->getMessage();
        }
        
        
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;
    }

}
