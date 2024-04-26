<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Setup\Patch\Data;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\CustomerAttributes\UpdateAttributeFromField;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\FieldToConfig\UpdateConfig;
use Amasty\CheckoutCore\Model\ResourceModel\Field\CollectionFactory;
use Amasty\CheckoutCore\Model\ResourceModel\GetCustomerAddressAttributeById;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SyncWithCheckoutFields implements DataPatchInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var GetCustomerAddressAttributeById
     */
    private $getCustomerAddressAttributeById;

    /**
     * @var UpdateConfig
     */
    private $updateConfig;

    /**
     * @var UpdateAttribute
     */
    private $updateAttributeFromField;

    public function __construct(
        CollectionFactory $collectionFactory,
        GetCustomerAddressAttributeById $getCustomerAddressAttributeById,
        UpdateConfig $updateConfig,
        UpdateAttributeFromField $updateAttributeFromField
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->getCustomerAddressAttributeById = $getCustomerAddressAttributeById;
        $this->updateConfig = $updateConfig;
        $this->updateAttributeFromField = $updateAttributeFromField;
    }

    public function apply()
    {
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter(Field::STORE_ID, Field::DEFAULT_STORE_ID);

        /** @var Field $field */
        foreach ($collection->getItems() as $field) {
            $this->updateConfig->execute($field);

            $attribute = $this->getCustomerAddressAttributeById->execute($field->getAttributeId());
            if ($attribute) {
                $this->updateAttributeFromField->execute($field, $attribute);
            }
        }

        return $this;
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [AddAttributesToManageCheckoutFields::class];
    }
}
