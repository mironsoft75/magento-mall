<?php

namespace Webkul\SpinToWin\Controller\Spin;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\ActionInterface;
use Webkul\SpinToWin\Model\ResourceModel\Reports\CollectionFactory as ReportsCollectionFactory;
use Silk\Integrations\Model\Session as UserSession;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Directory\Model\CountryFactory;

class Detail implements ActionInterface,HttpGetActionInterface{
    protected $_pageFactory;
    protected $ReportsCollectionFactory;
    protected $dataHelper;
    protected $_request;
    protected $userSession;
    protected $customerUrl;
    protected $resultRedirectFactory;
    protected $countryFactory;

	public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ReportsCollectionFactory $ReportsCollectionFactory,
        \Webkul\SpinToWin\Helper\Data $dataHelper,
        UserSession $userSession,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        CustomerUrl $customerUrl,
        CountryFactory $countryFactory
        )
	{
        $this->_request = $context->getRequest();
        $this->resultRedirectFactory= $context->getResultRedirectFactory();
        $this->ReportsCollectionFactory = $ReportsCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->userSession = $userSession;
		$this->_pageFactory = $pageFactory;
        $this->customerUrl = $customerUrl;
        $this->countryFactory = $countryFactory;
	}

    public function execute()
    {
        if($this->userSession->isLoggedIn()){
            $reportsId = $this->_request->getParam('reports_id');
            $customerId = $this->userSession->getId();
            $collection = $this->ReportsCollectionFactory->create();
            $collection->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('entity_id',$reportsId);
            $collection->getSelect()->joinLeft(['address' => $collection->getTable('spintowin_product_address')],
            'main_table.entity_id = address.reports_id','address.*')->limit(1);
            $c = $collection->getFirstItem();
            if(!$c->hasData()){
                throw new NoSuchEntityException(__("Spin Win Not Found %1",$reportsId));
            }
            $mediea = $this->dataHelper->getMediaDirectory();
            $detail= [
                'image' => $mediea.$c->getSegmentImage(),
                'spin_type' => $c->getSpinType(),
                'created_at' => $c->getCreatedAt(),
                'label' => $c->getSegmentLabel(),
                'reports_id' => $c->getId(),
                'address_id' => $c->getAddressId()
            ];
            if(!empty($detail['address_id'])){
                $detail['firstname']= $c->getFirstname();
                $detail['lastname'] = $c->getLastname();
                $detail['street'] = $c->getStreet();
                $detail['city'] = $c->getCity();
                $detail['country_name'] =$this->getCountryByCode($c->getCountryId());
                $detail['region'] = $c->getRegion();
                $detail['region_id'] = $c->getRegionId();
                $detail['postcode'] = $c->getPostcode();
                $detail['telephone'] = $c->getTelephone();
                $detail['email'] = $c->getEmail();
            }else {
                return $this->resultRedirectFactory->create()->setPath('*/*/address/reports_id/'.$reportsId);
            }
        }else {
            return $this->resultRedirectFactory->create()->setPath($this->customerUrl->getLoginUrl());
        }
        $page = $this->_pageFactory->create();
        $block = $page->getLayout()->getBlock('spintowin_product_win');
        $block->setDetail($detail);
        return $page;
    }

    public function getCountryByCode(string $countryCode): string
    {
        $country = $this->countryFactory->create();
        return $country->loadByCode($countryCode)->getName();
    }
}
