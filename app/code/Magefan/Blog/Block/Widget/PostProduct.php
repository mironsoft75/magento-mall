<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Widget;

use Magento\Catalog\Model\ProductRepository;

/**
 * Blog recent posts widget
 */
class PostProduct extends \Magefan\Blog\Block\Post\PostList\AbstractList implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var array
     */
    static $processedIds = [];

    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magefan\Blog\Model\Category
     */
    protected $_category;

    protected \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency;
    protected $productSrv;
    protected $_productFactory;
    protected $productRepository;
    protected $topSellersHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magefan\Blog\Model\Url $url
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magefan\Blog\Model\Url $url,
        \Silk\Integrations\Server\ProductSrv $productSrv,
        \Magefan\Blog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        ProductRepository $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Silk\Integrations\Helper\TopSellersHelper $topSellersHelper,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $postCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
        $this->priceCurrency = $priceCurrency;
        $this->_productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->productSrv = $productSrv;
        $this->topSellersHelper = $topSellersHelper;
    }

    /**
     * Set blog template
     *
     * @return string
     */
    public function _toHtml()
    {
        $this->setTemplate('Magefan_Blog::widget/post_product.phtml');

        return \Magento\Framework\View\Element\Template::_toHtml();

        /*$html = parent::_toHtml();

        return $html;*/
    }

    public function getPostProductInfo()
    {
        $return = [];
        $sku = $this->getData('sku');
        $code = $this->getData('code');
        if(!empty($sku)){
            $productId = $this->_productFactory->create()->getIdBySku($sku);
            $storeId = $this->_storeManager->getStore()->getId();
            if(empty($storeId)){
                $storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
            }

            try {
                $product = $this->productRepository->getById($productId,false,$storeId);
            }catch (\Exception $e){
                return $return;
            }
            $thumbnailImageUrl = $this->topSellersHelper->getProductImage($product);

            $price = $product->getPrice();
            $finalPrice = $product->getFinalPrice();
            if(empty($price)){
                $price = $product->getPriceInfo()->getPrice('regular_price')->getValue();
            }
            $save = $this->getSaveNumber($price,$finalPrice);
            $discount = $this->productSrv->getSavePrice($finalPrice, $price);

            $return = [
                'product_id'        => $productId,
                'name'              => $product->getName(),
                'image'             => $thumbnailImageUrl,
                'product_url'       => $product->getProductUrl(),
                'price_number'      => ($price != 0 && $price != $finalPrice) ? (float)$price : '',
                'final_price_number'=> $finalPrice,
                'price'             => ($price != 0 && $price != $finalPrice) ? $this->priceCurrency->format($price, false) : '',
                'final_price'       => $this->priceCurrency->format($finalPrice, false),
                'short_description' => $product->getShortDescription() ?? '',
                'save'              => $save,
                'discount'          => $discount,
                'code'              => $code
            ];
        }
        return $return;
    }

    /**
     * @param $originalPrice
     * @param $discountedPrice
     * @return float|int|string
     */
    public function getSaveNumber($originalPrice, $discountedPrice) {
        $discountPercentage = abs($originalPrice - $discountedPrice);
        if($discountPercentage == 0){
            return '';
        }else{
            return $discountPercentage;
        }
    }

    public function getProductById($productId)
    {
        return $this->_productFactory->create()->load($productId);
    }

}
