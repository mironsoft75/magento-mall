<?php
/**
 * GoogleShoppingFeed Admin Product Create Controller.
 * @category  Webkul
 * @package   Webkul_GoogleShoppingFeed
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\GoogleShoppingFeed\Controller\Adminhtml\Products;

use Magento\Backend\App\Action\Context;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Json\Helper\Data as JsonHelperData;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Api\StoreRepositoryInterface;
use Webkul\GoogleShoppingFeed\Helper\GoogleFeed as HelperGoogleFeed;
use Webkul\GoogleShoppingFeed\Logger\Logger;
use Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory;
use Webkul\GoogleShoppingFeed\Model\Storage\DbStorage;

class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelperData;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var \Webkul\GoogleShoppingFeed\Helper\GoogleFeed
     */
    private $helperGoogleFeed;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\GoogleFeedMapFactory
     */
    private $feedMap;

    /**
     * @var \Webkul\GoogleShoppingFeed\Model\Storage\DbStorage
     */
    private $dbStorage;

   /**
    * Init
    *
    * @param Context $context
    * @param JsonHelperData $jsonHelperData
    * @param StoreRepositoryInterface $storeRepository
    * @param DateTime $dateTime
    * @param Logger $logger
    * @param SearchCriteriaBuilder $searchCriteriaBuilder
    * @param ProductRepositoryInterface $productRepository
    * @param HelperGoogleFeed $helperGoogleFeed
    * @param GoogleFeedMapFactory $feedMap
    * @param DbStorage $dbStorage
    */
    public function __construct(
        Context $context,
        JsonHelperData $jsonHelperData,
        StoreRepositoryInterface $storeRepository,
        DateTime $dateTime,
        Logger $logger,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        HelperGoogleFeed $helperGoogleFeed,
        GoogleFeedMapFactory $feedMap,
        DbStorage $dbStorage
    ) {
        parent::__construct($context);
        $this->jsonHelperData = $jsonHelperData;
        $this->logger = $logger;
        $this->storeRepository = $storeRepository;
        $this->dateTime = $dateTime;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->helperGoogleFeed = $helperGoogleFeed;
        $this->feedMap = $feedMap;
        $this->dbStorage = $dbStorage;
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        try {
            $accessToken = $this->helperGoogleFeed->helperData->getAccessToken();

            if ($this->getRequest()->isPost() && $accessToken) {
                $result = $this->export($accessToken);
            } else {
                $result = ['error' => 1, 'message' => __('Invalid request / Google feed account not authenticated.')];
            }
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        } catch (\Exception $e) {
            $result = ['error' => 1, 'message' => $e->getMessage()];
            $this->getResponse()->representJson($this->jsonHelperData->jsonEncode($result));
        }
    }

    /**
     * Check product import permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_GoogleShoppingFeed::product_export');
    }
    /**
     * Export
     *
     * @param mixed $accessToken
     * @return array
     */
    public function export($accessToken)
    {
        $mappedPro = $this->feedMap->create()->getCollection()->getColumnValues('mage_pro_id');
        $mappedPro = empty($mappedPro) ? [0] : $mappedPro;
        $data = $this->getRequest()->getParams();
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('visibility', 1, 'neq')
            ->addFilter('type_id', ['grouped', 'bundle'], 'nin')
            ->addFilter('entity_id', $mappedPro, 'nin')
            ->setCurrentPage(1)->setPageSize(10)->create();
        $productsList = $this->productRepository->getList($searchCriteria)->getItems();

        $configDetails = $this->helperGoogleFeed->helperData->getConfigDetails();
        $storeList = $configDetails['according_store'] == 1 ?
        $this->storeRepository->getList() : [$this->storeRepository->getById(0)];
        if (!empty($productsList) && !isset($data['finish'])) {
            $returnData = $this->insertGoogleFeed($productsList, $configDetails, $storeList);
            $total = $returnData['total'];
            $result = $returnData['result'];
        } else {
            $total = (int) $data['count'] - (int) $data['skip'];
            $msg = '<div class="wk-mu-success wk-mu-box">' . __('Total Product(s) Created.') . '</div>';
            $msg .= '<div class="wk-mu-note wk-mu-box">' . __('Finished Execution.') . '</div>';
            $result['message'] = $msg;
        }
        return $result;
    }
    /**
     * _insertGoogleFeed
     *
     * @param mixed $productsList
     * @param mixed $configDetails
     * @param mixed $storeList
     * @return array
     */
    public function insertGoogleFeed($productsList, $configDetails, $storeList)
    {
        $syncsFeeds = [];
        foreach ($productsList as $product) {
            $resultData = $this->processFeedExport($product, $storeList, $configDetails, $syncsFeeds);
            $total = $resultData['total'];
            $result = $resultData['result'];
            $syncsFeeds = $resultData['syncfeeds'];
        }

        if (!empty($syncsFeeds)) {
            $this->dbStorage->insertMultiple($syncsFeeds, 'google_feed_product_map');
        } else {
            $result = [
                'error' => 1,
                'total' => 0,
                'message' => __('No products for export.'),
            ];
        }
        return ['result' => $result, 'total' => $total];
    }
    /**
     * Feed Export
     *
     * @param mixed $product
     * @param mixed $storeList
     * @param mixed $configDetails
     * @param mixed $syncsFeeds
     * @return array
     */
    public function processFeedExport($product, $storeList, $configDetails, $syncsFeeds)
    {
        $items = [];

        $total = 0;
        foreach ($storeList as $store) {
            if ($store->getId() || $configDetails['according_store'] == 0) {
                $storeDetailForFeed = $this->helperGoogleFeed->getStoreDetailForFeed($store);
                $storeDetailForFeed['store_id'] = $store->getId() == 0 ? 0 : $storeDetailForFeed['store_id'];
                $productTmp = $this->productRepository->getById(
                    $product->getEntityId(),
                    false,
                    $storeDetailForFeed['store_id']
                );
                if ($product->getTypeId() == 'configurable') {
                    $googleFeedMap = $this->feedMap->create()->getCollection()->addFieldToFilter(
                        'mage_pro_id',
                        ['eq' => $product->getEntityId()]
                    )->setPageSize(1)->getFirstItem();
                    $result = $this->helperGoogleFeed->processConfigForExport(
                        $product,
                        $googleFeedMap,
                        $storeDetailForFeed,
                    );
                } else {
                    $availability = $productTmp->getQuantityAndStockStatus();
                    $availability = isset($availability['qty']) && $availability['qty'] ? true : false;
                    $productWebsId = $productTmp->getWebsiteIds();
                    array_push($productWebsId, 0);

                    if ($productTmp->getStatus() == 1 && $availability && $store->getIsActive()
                        && in_array($store->getWebsiteId(), $productWebsId)) {
                        $feedProduct = $this->helperGoogleFeed
                            ->getProductForFeed($productTmp, $storeDetailForFeed);
                        $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                        $expireDate = $this->dateTime->gmtDate('Y-m-d H:i:s', strtotime("+30 days"));
                        $items = $this->helperGoogleFeed->insertFeedToGoogleShop($feedProduct);
                        $this->logger->info("item: " . \json_encode($items) . "\n");
                        if ($items['error'] == 0) {
                            $total++;
                            $syncsFeeds[] = [
                                'feed_id' => $items['product']['id'],
                                'mage_pro_id' => $productTmp->getEntityId(),
                                'expired_at' => $expireDate,
                                'store_id' => $storeDetailForFeed['store_id'],
                            ];
                            $result = ['error' => 0, 'total' => $total];
                        } else {
                            $total++;
                            $errorMessage = $this->jsonHelperData->JsonDecode($items['message']);
                            $result = [
                                'error' => 1,
                                'total' => $total,
                                'message' => $errorMessage['error']['message'],
                            ];
                        }
                    } else {
                        $result = ['error' => 0, 'total' => $total];
                    }
                }
            }
        }

        return ['result' => $result, 'total' => $total, 'syncfeeds' => $syncsFeeds];
    }
}
