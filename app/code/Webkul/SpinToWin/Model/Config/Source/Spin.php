<?php
namespace Webkul\SpinToWin\Model\Config\Source;
use Magento\Framework\Data\OptionSourceInterface;
use Webkul\SpinToWin\Model\ResourceModel\Info\CollectionFactory;

class Spin implements \Magento\Framework\Option\ArrayInterface{

    protected CollectionFactory $collectionFactory;
    /**
     * @var array
     */
    protected $options;

    public function __construct(
        CollectionFactory $collectionFactory
        )
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->collectionFactory->create()->toOptionArray();
        }
        return $this->options;
    }

}