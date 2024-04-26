<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\CustomerAttributes\UpdateAttributeFromField;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\FieldToConfig\UpdateConfig;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\GetCustomerAddressAttributeById;
use Magento\Framework\Exception\AlreadyExistsException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SaveField
{
    /**
     * @var FieldResource
     */
    private $fieldResource;

    /**
     * @var GetCustomerAddressAttributeById
     */
    private $getCustomerAddressAttributeById;

    /**
     * @var UpdateConfig
     */
    private $updateConfig;

    /**
     * @var UpdateAttributeFromField
     */
    private $updateAttributeFromField;

    /**
     * @var ProcessCustomFieldAttribute
     */
    private $processCustomFieldAttribute;

    /**
     * @var string[]
     */
    private $allowedKeys;

    /**
     * @param FieldResource $fieldResource
     * @param GetCustomerAddressAttributeById $getCustomerAddressAttributeById
     * @param UpdateConfig $updateConfig
     * @param UpdateAttributeFromField $updateAttributeFromField
     * @param ProcessCustomFieldAttribute $processCustomFieldAttribute
     * @param string[] $allowedKeys
     */
    public function __construct(
        FieldResource $fieldResource,
        GetCustomerAddressAttributeById $getCustomerAddressAttributeById,
        UpdateConfig $updateConfig,
        UpdateAttributeFromField $updateAttributeFromField,
        ProcessCustomFieldAttribute $processCustomFieldAttribute,
        array $allowedKeys = []
    ) {
        $this->fieldResource = $fieldResource;
        $this->getCustomerAddressAttributeById = $getCustomerAddressAttributeById;
        $this->updateConfig = $updateConfig;
        $this->updateAttributeFromField = $updateAttributeFromField;
        $this->processCustomFieldAttribute = $processCustomFieldAttribute;
        $this->allowedKeys = $allowedKeys;
    }

    /**
     * @param Field $field
     * @param array $fieldData
     * @throws AlreadyExistsException
     * @throws \UnexpectedValueException
     * @SuppressWarnings(PHPMD.MissingImport)
     */
    public function execute(Field $field, array $fieldData): void
    {
        if (empty($this->allowedKeys)) {
            throw new \UnexpectedValueException('No keys were allowed');
        }

        if (empty($fieldData)) {
            return;
        }

        $allowedKeys = array_flip($this->allowedKeys);
        if ((int) $fieldData[Field::ENABLED] === 0) {
            unset($allowedKeys[Field::SORT_ORDER]);
        }

        $field->addData(array_intersect_key($fieldData, $allowedKeys));
        $this->fieldResource->save($field);

        if ($field->getStoreId() === Field::DEFAULT_STORE_ID) {
            $this->updateConfig->execute($field);

            $attribute = $this->getCustomerAddressAttributeById->execute($field->getAttributeId());
            if ($attribute) {
                $this->updateAttributeFromField->execute($field, $attribute);
            }
        }

        $this->processCustomFieldAttribute->execute($field);
    }
}
