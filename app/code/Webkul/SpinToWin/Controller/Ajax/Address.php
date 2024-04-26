<?php
namespace Webkul\SpinToWin\Controller\Ajax;

use Exception;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Silk\Integrations\Model\Session as UserSession;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Silk\Integrations\Server\ForumSrv;
use Webkul\SpinToWin\Model\ResourceModel\ProductAddressFactory;
use Webkul\SpinToWin\Model\ProductAddressFactory as ProductAddressModelFactory;
use Webkul\SpinToWin\Helper\ProductEmail;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory as ReportsCollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
class Address implements ActionInterface,HttpPostActionInterface{

    protected $jsonFactory;
    protected $dataHelper;
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;
    protected $_request;
    protected $userSession;
    protected $productAddressFactory;
    protected $productEmail;
    protected ForumSrv $forumSrv;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Webkul\SpinToWin\Helper\Data $dataHelper,
        UserSession $userSession,
        AddressRepositoryInterface $addressRepository,
        ResultFactory $jsonFactory,
        ProductAddressFactory $productAddressFactory,
        ProductAddressModelFactory $productAddressModelFactory,
        ReportsCollectionFactory $ReportsCollectionFactory,
        ProductEmail $productEmail,
        ForumSrv $forumSrv
    ){
        $this->_request = $context->getRequest();
        $this->dataHelper = $dataHelper;
        $this->addressRepository = $addressRepository;
        $this->userSession = $userSession;
        $this->jsonFactory = $jsonFactory;
        $this->productAddressFactory = $productAddressFactory;
        $this->productAddressModelFactory = $productAddressModelFactory;
        $this->reportsCollectionFactory = $ReportsCollectionFactory;
        $this->productEmail = $productEmail;
        $this->forumSrv = $forumSrv;
    }

    public function execute(){
        if($this->userSession->isLoggedIn()){
            $customer = $this->userSession->getCustomer();
            $reportsId = $this->_request->getParam('reports_id');
            $addressId = $this->_request->getParam('address_id');
            $isShare = $this->_request->getParam('is_share') ?? 0;
            $customerId = $this->userSession->getId();
            $collection = $this->reportsCollectionFactory->create();
            $collection->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('entity_id',$reportsId);
            $collection->getSelect()->joinLeft(['address' => $collection->getTable('spintowin_product_address')],
            'main_table.entity_id = address.reports_id','address.*')->limit(1);
            $c = $collection->getFirstItem();
            if(!$c->hasData()){
                throw new NoSuchEntityException(__("Spin Win Not Found %1",$reportsId));
            }
            if($c->getAddressId()){
                throw new Exception(__("Spin Win has bind"));
            }
            $addressData = $this->addressRepository->getById($addressId);
            $address= [
                'reports_id' => $reportsId,
                'email' => $customer->getEmail(),
                'firstname' => $addressData->getFirstname(),
                'lastname' => $addressData->getLastname(),
                'street' => $addressData->getStreet()[0],
                'country_id' => $addressData->getCountryId(),
                'city' => $addressData->getCity(),
                'region' => $addressData->getRegion()->getRegion(),
                'region_id' => $addressData->getRegionId(),
                'postcode' => $addressData->getPostcode(),
                'telephone' => $addressData->getTelephone()
            ];
            $model = $this->productAddressModelFactory->create();
            $model->setData($address);
            $resourceModel = $this->productAddressFactory->create();
            
            $resourceModel->save($model);
            //Send Email
            $this->productEmail->sendNotification($address['email']);
            if($isShare){
                $this->forumSrv->sendToForum($c->getSegmentProductSku(),1); 
            }
        }

        $back= ['code' => 200,'msg' => __("Prize(s) will be sent to you ASAP! Please be patience")];
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
        return $resultJson;
    }

}
