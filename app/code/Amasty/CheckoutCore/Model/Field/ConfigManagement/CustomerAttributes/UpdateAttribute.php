<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\ConfigManagement\CustomerAttributes;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\WebsiteRepositoryInterface;

class UpdateAttribute
{
    public const DEFAULT_WEBSITE_ID = 0;

    /**
     * @var WebsiteRepositoryInterface
     */
    private $websiteRepository;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    public function __construct(
        WebsiteRepositoryInterface $websiteRepository,
        AttributeResource $attributeResource
    ) {
        $this->websiteRepository = $websiteRepository;
        $this->attributeResource = $attributeResource;
    }

    /**
     * @param Attribute $attribute
     * @param bool $isEnabled
     * @param bool $isRequired
     * @param int $websiteId
     * @return void
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function execute(Attribute $attribute, bool $isEnabled, bool $isRequired, int $websiteId): void
    {
        if ($websiteId === self::DEFAULT_WEBSITE_ID) {
            $attribute->setData('is_visible', $isEnabled);
            $attribute->setIsRequired($isRequired);
        } else {
            $website = $this->websiteRepository->getById($websiteId);

            $attribute->setWebsite($website);
            $attribute->setData('scope_is_visible', $isEnabled);
            $attribute->setData('scope_is_required', $isRequired);
        }

        $this->attributeResource->save($attribute);
    }
}
