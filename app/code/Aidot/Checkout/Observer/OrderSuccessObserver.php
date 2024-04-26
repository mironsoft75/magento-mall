<?php

namespace Aidot\Checkout\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\ResourceModel\Quote\Item\Option\CollectionFactory as OptionCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class OrderSuccessObserver implements ObserverInterface {

    protected $customerSession;
    protected $quoteFactory;
    protected $storeManager;
    protected $customerRepository;
    protected $optionCollectionFactory;
    protected $productCollectionFactory;
    protected $quoteRepository;
    protected $logger;
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        OptionCollectionFactory $optionCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        CartRepositoryInterface  $quoteRepository,
        LoggerInterface $logger
    )
    {
        $this->customerSession = $customerSession;
        $this->quoteFactory = $quoteFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    

    public function execute(Observer $observer)
    {
        $orders = $observer->getOrder();
        $quoteId = $orders->getQuoteId();
        $quote = $this->quoteRepository->get($quoteId);
        $orderItems = $orders->getItems();
        $quoteItems = $quote->getAllItems();
        if(count($orderItems) != count($quoteItems)){
            $orderSku = [];
            foreach($orderItems as $orderItem){
                $orderSku[$orderItem->getSku()] = 1;
            }
            $newQuote = $this->quoteFactory->create();
            $storeId = $this->storeManager->getStore()->getId();
            $newQuote->setStore($this->storeManager->getStore());
            if ($this->customerSession->isLoggedIn()) {
                $newQuote->setCustomer($this->customerRepository->getById($this->customerSession->getCustomerId()));
            }
            foreach($quoteItems as $quoteItem){
                if(!isset($orderSku[$quoteItem->getSku()])){
                    if(empty($quoteItem->getParentItemId())){
                        $option = $this->optionCollectionFactory->create()->addFieldToFilter('code','info_buyRequest')->addItemFilter($quoteItem)->getFirstItem();
                        if($option->hasData()){
                            try{
                                $value = new \Magento\Framework\DataObject(json_decode($option->getValue(),true));
                                $product = $this->getProducts($storeId,[$quoteItem->getProductId()]);
                                $newQuote->addProduct($product,$value);
                            }catch(Exception $e){
                                $this->logger->error('add quote item error : '.$e->getMessage());
                            }
                            
                        }
                    }
                }
            }
            $newQuote->collectTotals()->save();
        }
    }

   private function getProducts(string $storeId, array $orderItemProductIds)
   {
      
       $collection = $this->productCollectionFactory->create();
       $collection->setStore($storeId)
           ->addIdFilter($orderItemProductIds)
           ->addStoreFilter()
           ->addAttributeToSelect('*')
           ->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner')
           ->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner')
           ->addOptionsToResult();

       return $collection->getFirstItem();
   }
}