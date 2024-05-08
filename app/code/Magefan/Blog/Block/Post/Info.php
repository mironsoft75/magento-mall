<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Post;

use Magento\Store\Model\ScopeInterface;

/**
 * Blog post info block
 */
class Info extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Post\Collection
     */
    protected $_postCollection;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollectionFactory;

    protected $_postCollectionFactory;

    /**
     * Constructor
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry                      $coreRegistry
     * @param \Magento\Framework\Locale\ResolverInterface      $localeResolver
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_postCollectionFactory = $postCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Block template file
     * @var string
     */
    protected $_template = 'Magefan_Blog::post/info.phtml';

    /**
     * Retrieve formated posted date
     * @var string
     * @deprecated Use $post->getPublishDate() instead
     * @return string
     */
    public function getPostedOn($format = 'Y-m-d H:i:s')
    {
        return $this->getPost()->getPublishDate($format);
    }

    /**
     * Retrieve 1 if display author information is enabled
     * @return int
     */
    public function authorEnabled()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfblog/author/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve 1 if author page is enabled
     * @return int
     */
    public function authorPageEnabled()
    {
        return (int) $this->_scopeConfig->getValue(
            'mfblog/author/page_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve true if magefan comments are enabled
     * @return bool
     */
    public function magefanCommentsEnabled()
    {
        return $this->_scopeConfig->getValue(
            'mfblog/post_view/comments/type',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == \Magefan\Blog\Model\Config\Source\CommetType::MAGEFAN;
    }

    /**
     * @return bool
     */
    public function viewsCountEnabled()
    {
        return (bool)$this->_scopeConfig->getValue(
            'mfblog/post_view/views_count/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve posts instance
     *
     * @return \Magefan\Blog\Model\Category
     */
    public function getPost()
    {
        if (!$this->hasData('post')) {
            $this->setData(
                'post',
                $this->_coreRegistry->registry('current_blog_post')
            );
        }
        return $this->getData('post');
    }

    /**
     * @param $postId
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryNameByPostId($postId)
    {
        $return = null;
        $this->_postCollection = $this->_postCollectionFactory->create();
        $connection = $this->_postCollection->getConnection();
        $select = $connection->select()
            ->from(
                ['pc' => $this->_postCollection->getTable('magefan_blog_post_category')],
                ['category_id']
            )->joinLeft(
                ['p' => $this->_postCollection->getTable('magefan_blog_post')],
                'pc.post_id = p.post_id',
                ['p.post_id']
            )->where('p.post_id = ?', $postId);
        $result = $connection->fetchAll($select);
        if(!empty($result)){
            $categoryIds = array_column($result,'category_id');
            $subCategories = $this->categoryCollectionFactory->create();
            $subCategories
                ->addActiveFilter()
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position')
                ->addFieldToFilter('category_id', ['in' => $categoryIds]);
            foreach ($subCategories->getItems() as $item){
                $return .= $item->getTitle().',';
            }
            $return = trim($return,',');
        }
        return $return;
    }
}
