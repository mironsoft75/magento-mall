<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Block\Sidebar;

/**
 * Blog sidebar categories block
 */
class Recent extends \Magefan\Blog\Block\Post\PostList\AbstractList
{
    use Widget;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollectionFactory;

    /**
     * @var string
     */
    protected $_widgetKey = 'recent_posts';

    /**
     * AbstractList constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory
     * @param \Magefan\Blog\Model\Url $url
     * @param array $data
     * @param null $config
     * @param null $templatePool
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magefan\Blog\Model\ResourceModel\Post\CollectionFactory $postCollectionFactory,
        \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magefan\Blog\Model\Url $url,
        array $data = [],
        $config = null,
        $templatePool = null
    ) {
        parent::__construct($context,$coreRegistry,$filterProvider,$postCollectionFactory,$url, $data,$config,$templatePool);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return $this
     */
    public function _construct()
    {
        $this->setPageSize(
            (int) $this->_scopeConfig->getValue(
                'mfblog/sidebar/'.$this->_widgetKey.'/posts_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        return parent::_construct();
    }

    /**
     * Prepare posts collection
     *
     * @return void
     */
    protected function _preparePostCollection()
    {
        parent::_preparePostCollection();
        $this->_postCollection->addRecentFilter();
    }

    /**
     * Retrieve true if display the post image is enabled in the config
     * @return bool
     */
    public function getDisplayImage()
    {
        return (bool)$this->_scopeConfig->getValue(
            'mfblog/sidebar/'.$this->_widgetKey.'/display_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        $templateName = (string)$this->_scopeConfig->getValue(
            'mfblog/sidebar/'.$this->_widgetKey.'/template',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($template = $this->templatePool->getTemplate('blog_post_sidebar_posts', $templateName)) {
            $this->_template = $template;
        }
        return parent::getTemplate();
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
