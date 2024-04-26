<?php

namespace Webkul\SpinToWin\Ui\DataProvider\Code\Form;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\Request\DataPersistorInterface;
use Webkul\SpinToWin\Model\ResourceModel\Code\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class CloudCodeDataProvider extends AbstractDataProvider {
    /**
     * @var Webkul\SpinToWin\Model\ResourceModel\Code\CollectionFactory
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;


    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    protected $storeManager;

    /**
     * CommentDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $commentCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param \Magento\Framework\UrlInterface $url
     * @param array $meta
     * @param array $data
     * @param \Magento\Framework\Escaper|null $escaper
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = [],
        \Magento\Framework\Escaper $escaper = null
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);


        $this->escaper = $escaper ?: \Magento\Framework\App\ObjectManager::getInstance()->create(
            \Magento\Framework\Escaper::class
        );
        $this->storeManager = $storeManager;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        return $items;
    }
    
}