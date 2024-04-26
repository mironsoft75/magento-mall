<?php
namespace Amasty\CheckoutCore\Plugin\Sales\Api\Model;
use Amasty\CheckoutCore\Model\ResourceModel\OrderCustomFields\CollectionFactory;
use Amasty\CheckoutCore\Api\Data\CustomFieldsConfigInterface;
use Amasty\CheckoutCore\Api\Data\OrderCustomFieldsInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionFactory;

class Order {

     /**
     * @var CollectionFactory
     */
    private $orderCustomFieldsCollection;

    private OrderAddressExtensionFactory $orderAddressExtensionFactory;

    public function __construct(
        CollectionFactory $orderCustomFieldsCollection,
        OrderAddressExtensionFactory $orderAddressExtensionFactory
    ) {
        $this->orderCustomFieldsCollection = $orderCustomFieldsCollection;
        $this->orderAddressExtensionFactory = $orderAddressExtensionFactory;
    }

    
    public function afterGetShippingAddress(\Magento\Sales\Model\Order $subject,\Magento\Sales\Model\Order\Address $address=null)
    {
        if($address){
            $address = $this->getCustomField($address);
        }
        return $address;
    }

    private function getCustomField($address){
        $extensionAttributes = $address->getExtensionAttributes();
        if($extensionAttributes && $extensionAttributes->getCustomField1()){
            return $address;
        }
        $addressExtension = $extensionAttributes ? $extensionAttributes : $this->orderAddressExtensionFactory->create();
        $countOfCustomFields = CustomFieldsConfigInterface::COUNT_OF_CUSTOM_FIELDS;
        $index = CustomFieldsConfigInterface::CUSTOM_FIELD_INDEX;
        $formValues = $address->getData();
        for ($index; $index <= $countOfCustomFields; $index++) {
            /** @var \Amasty\CheckoutCore\Model\ResourceModel\OrderCustomFields\Collection $orderCustomFieldsCollection */
            $orderCustomFieldsCollection = $this->orderCustomFieldsCollection->create();
            $orderCustomFieldsCollection->addFieldByOrderIdAndCustomField(
                $formValues['parent_id'],
                'custom_field_' . $index
            );
            $orderCustomFieldsData = $orderCustomFieldsCollection->getFirstItem()->getData();

            if ($orderCustomFieldsData) {
                if('custom_field_1' == $orderCustomFieldsData[OrderCustomFieldsInterface::NAME]){
                    $addressExtension->setCustomField1($orderCustomFieldsData[$formValues['address_type'] . '_value']);
                }
            }
        }
        return $address->setExtensionAttributes($addressExtension);
    }

}