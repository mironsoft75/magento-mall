<?php
namespace Webkul\SpinToWin\Block\Address;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Customer\Helper\Session\CurrentCustomer;

class AddressList extends \Magento\Framework\View\Element\Template
{
    private $currentCustomer;
    private $addressCollectionFactory;
    private $countryFactory;
    private $addressCollection;
    private $logger;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        LoggerInterface $logger,
        CurrentCustomer $currentCustomer,
        AddressCollectionFactory $addressCollectionFactory,
        CountryFactory $countryFactory
    ) {
        parent::__construct($context);
        $this->logger = $logger;
        $this->currentCustomer = $currentCustomer;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->countryFactory = $countryFactory;
    }
    public function getCustomer(): \Magento\Customer\Api\Data\CustomerInterface
    {
        $customer = $this->getData('customer');
        if ($customer === null) {
            $customer = $this->currentCustomer->getCustomer();
            $this->setData('customer', $customer);
        }
        return $customer;
    }

    public function getAddresses()
    {
        $addressesList = [];
        try {
            $addresses = $this->getAddressCollection();
            foreach ($addresses as $address) {
                $addressesList[] = $address->getDataModel();
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $addressesList;
    }

    private function getAddressCollection(): \Magento\Customer\Model\ResourceModel\Address\Collection
    {
        if (null === $this->addressCollection) {
            if (null === $this->getCustomer()) {
                throw new NoSuchEntityException(__('Customer not logged in'));
            }
            $collection = $this->addressCollectionFactory->create();
            $collection->setOrder('entity_id', 'desc');
            $collection->setCustomerFilter([$this->getCustomer()->getId()]);
            $this->addressCollection = $collection;
        }
        return $this->addressCollection;
    }

    public function getCountryByCode(string $countryCode): string
    {
        $country = $this->countryFactory->create();
        return $country->loadByCode($countryCode)->getName();
    }

    public function getStreetAddress(\Magento\Customer\Api\Data\AddressInterface $address): string
    {
        $street = $address->getStreet();
        if (is_array($street)) {
            $street = implode(', ', $street);
        }
        return $street;
    }
}
