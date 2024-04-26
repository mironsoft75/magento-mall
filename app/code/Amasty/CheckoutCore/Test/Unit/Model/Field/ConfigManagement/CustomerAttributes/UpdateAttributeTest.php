<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Test\Unit\Model\Field\ConfigManagement\CustomerAttributes;

use Amasty\CheckoutCore\Model\Field\ConfigManagement\CustomerAttributes\UpdateAttribute;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see UpdateAttribute
 * @covers UpdateAttribute::execute
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class UpdateAttributeTest extends \PHPUnit\Framework\TestCase
{
    private const WEBSITE_ID = 1;

    /**
     * @var WebsiteRepositoryInterface|MockObject
     */
    private $websiteRepositoryTest;

    /**
     * @var AttributeResource|MockObject
     */
    private $attributeResourceMock;

    /**
     * @var UpdateAttribute
     */
    private $subject;

    protected function setUp(): void
    {
        $this->websiteRepositoryTest = $this->createMock(WebsiteRepositoryInterface::class);
        $this->attributeResourceMock = $this->createMock(AttributeResource::class);

        $this->subject = new UpdateAttribute(
            $this->websiteRepositoryTest,
            $this->attributeResourceMock
        );
    }

    /**
     * @param bool $isEnabled
     * @param bool $isRequired
     * @return void
     * @dataProvider generalDataProvider
     */
    public function testExecute(bool $isEnabled, bool $isRequired): void
    {
        $attributeMock = $this->createMock(Attribute::class);

        $attributeMock
            ->expects($this->once())
            ->method('setData')
            ->with('is_visible', $isEnabled);

        $attributeMock
            ->expects($this->once())
            ->method('setIsRequired')
            ->with($isRequired);

        $this->attributeResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($attributeMock);

        $attributeMock->expects($this->never())->method('setWebsite');
        $this->websiteRepositoryTest->expects($this->never())->method('getById');

        $this->subject->execute(
            $attributeMock,
            $isEnabled,
            $isRequired,
            UpdateAttribute::DEFAULT_WEBSITE_ID
        );
    }

    /**
     * @param bool $isEnabled
     * @param bool $isRequired
     * @return void
     * @dataProvider generalDataProvider
     */
    public function testExecuteWithWebsite(bool $isEnabled, bool $isRequired): void
    {
        $attributeMock = $this->createMock(Attribute::class);
        $websiteMock = $this->createMock(WebsiteInterface::class);

        $attributeMock
            ->expects($this->exactly(2))
            ->method('setData')
            ->withConsecutive(
                ['scope_is_visible', $isEnabled],
                ['scope_is_required', $isRequired]
            );

        $this->websiteRepositoryTest
            ->expects($this->once())
            ->method('getById')
            ->with(self::WEBSITE_ID)
            ->willReturn($websiteMock);

        $attributeMock
            ->expects($this->once())
            ->method('setWebsite')
            ->with($websiteMock);

        $this->attributeResourceMock
            ->expects($this->once())
            ->method('save')
            ->with($attributeMock);

        $this->subject->execute(
            $attributeMock,
            $isEnabled,
            $isRequired,
            self::WEBSITE_ID
        );
    }

    public function generalDataProvider(): array
    {
        return [
            [false, false],
            [false, true],
            [true, false],
            [true, true]
        ];
    }
}
