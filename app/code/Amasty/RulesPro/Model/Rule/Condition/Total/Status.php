<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Special Promotions Pro for Magento 2
*/

namespace Amasty\RulesPro\Model\Rule\Condition\Total;

use Magento\Rule\Model\Condition\Context;
use Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory;

/**
 * Product rule condition data model
 */
class Status extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->setType(Status::class)->setValue(null);
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $statuses = array_column(
            $this->collectionFactory->create()->toOptionArray(),
            'label',
            'value'
        );
        $options = array_merge($this->getAttributeOptions(), $statuses);
        $this->setAttributeOption($options);

        return $this;
    }

    /**
     * @return $this
     */
    public function loadOperatorOptions()
    {
        $this->setOperatorOption(
            [
                '=' => __('is'),
                '<>' => __('is not'),
            ]
        );

        return $this;
    }

    /**
     * @return string
     */
    public function asHtml()
    {
        $html = $this->getTypeElement()->getHtml() .
            __(
                sprintf(
                    "Order Status %s %s",
                    $this->getOperatorElement()->getHtml(),
                    $this->getAttributeElement()->getHtml()
                )
            );
        if ($this->getId() != '1') {
            $html .= $this->getRemoveLinkHtml();
        }

        return $html;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $model
     *
     * @return array|bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $result = ['status' => $this->getOperatorForValidate() . "'" . $this->getAttributeElement()->getValue() . "'"];

        return $result;
    }
}
