<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\ResourceModel\Field\Collection\FilterByAttributeAndStore;
use Amasty\CheckoutCore\Model\ResourceModel\Field\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class GetDefaultField
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FilterByAttributeAndStore
     */
    private $filterByAttributeAndStore;

    public function __construct(
        CollectionFactory $collectionFactory,
        FilterByAttributeAndStore $filterByAttributeAndStore
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filterByAttributeAndStore = $filterByAttributeAndStore;
    }

    public function execute(int $attributeId): ?Field
    {
        $collection = $this->collectionFactory->create();
        $this->filterByAttributeAndStore->execute(
            $collection,
            $attributeId,
            [Field::DEFAULT_STORE_ID]
        );

        return $collection->getSize() > 0 ? $collection->getFirstItem() : null;
    }
}
