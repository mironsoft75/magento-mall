<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Test\Unit\Model\Field\Form;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\CustomerAttributes\UpdateAttributeFromField;
use Amasty\CheckoutCore\Model\Field\ConfigManagement\FieldToConfig\UpdateConfig;
use Amasty\CheckoutCore\Model\Field\Form\ProcessCustomFieldAttribute;
use Amasty\CheckoutCore\Model\Field\Form\SaveField;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\GetCustomerAddressAttributeById;
use Magento\Customer\Model\Attribute;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @see SaveField
 * @covers SaveField::execute
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class SaveFieldTest extends \PHPUnit\Framework\TestCase
{
    private const ATTRIBUTE_ID = '42';
    private const STORE_ID = 1;

    /**
     * @var FieldResource|MockObject
     */
    private $fieldResourceMock;

    /**
     * @var GetCustomerAddressAttributeById|MockObject
     */
    private $getCustomerAddressAttributeByIdMock;

    /**
     * @var UpdateConfig|MockObject
     */
    private $updateConfigMock;

    /**
     * @var UpdateAttributeFromField|MockObject
     */
    private $updateAttributeFromFieldMock;

    /**
     * @var ProcessCustomFieldAttribute|MockObject
     */
    private $processCustomFieldAttributeMock;

    /**
     * @var Field|MockObject
     */
    private $fieldMock;

    protected function setUp(): void
    {
        $this->fieldResourceMock = $this->createMock(FieldResource::class);
        $this->getCustomerAddressAttributeByIdMock = $this->createMock(GetCustomerAddressAttributeById::class);
        $this->updateConfigMock = $this->createMock(UpdateConfig::class);
        $this->updateAttributeFromFieldMock = $this->createMock(UpdateAttributeFromField::class);
        $this->processCustomFieldAttributeMock = $this->createMock(ProcessCustomFieldAttribute::class);
        $this->fieldMock = $this->createMock(Field::class);
    }

    public function testExecuteNoAllowedKeys(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('No keys were allowed');

        $this->fieldMock->expects($this->never())->method('addData');
        $this->fieldResourceMock->expects($this->never())->method('save');
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->updateConfigMock->expects($this->never())->method('execute');
        $this->updateAttributeFromFieldMock->expects($this->never())->method('execute');
        $this->processCustomFieldAttributeMock->expects($this->never())->method('execute');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->updateConfigMock,
            $this->updateAttributeFromFieldMock,
            $this->processCustomFieldAttributeMock,
            []
        );

        $subject->execute($this->fieldMock, ['attribute_id' => self::ATTRIBUTE_ID]);
    }

    public function testExecuteWithNoFieldData(): void
    {
        $this->fieldMock->expects($this->never())->method('addData');
        $this->fieldResourceMock->expects($this->never())->method('save');
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->updateConfigMock->expects($this->never())->method('execute');
        $this->updateAttributeFromFieldMock->expects($this->never())->method('execute');
        $this->processCustomFieldAttributeMock->expects($this->never())->method('execute');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->updateConfigMock,
            $this->updateAttributeFromFieldMock,
            $this->processCustomFieldAttributeMock,
            ['some_key']
        );

        $subject->execute($this->fieldMock, []);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithDefaultStoreIdDataProvider
     */
    public function testExecuteWithDefaultStoreId(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $this->fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);
        $this->fieldMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(Field::DEFAULT_STORE_ID);
        $this->fieldMock
            ->expects($this->once())
            ->method('getAttributeId')
            ->willReturn((int) self::ATTRIBUTE_ID);

        $this->updateConfigMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock);

        $this->processCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock);

        $this->getCustomerAddressAttributeByIdMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) self::ATTRIBUTE_ID)
            ->willReturn(null);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($this->fieldMock);
        $this->updateAttributeFromFieldMock->expects($this->never())->method('execute');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->updateConfigMock,
            $this->updateAttributeFromFieldMock,
            $this->processCustomFieldAttributeMock,
            $allowedKeys
        );

        $subject->execute($this->fieldMock, $fieldData);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithDefaultStoreIdDataProvider
     */
    public function testExecuteWithDefaultStoreIdAndAttribute(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $this->fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);
        $this->fieldMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(Field::DEFAULT_STORE_ID);
        $this->fieldMock
            ->expects($this->once())
            ->method('getAttributeId')
            ->willReturn((int) self::ATTRIBUTE_ID);

        $this->updateConfigMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock);

        $this->processCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock);

        $attributeMock = $this->createMock(Attribute::class);
        $this->getCustomerAddressAttributeByIdMock
            ->expects($this->once())
            ->method('execute')
            ->with((int) self::ATTRIBUTE_ID)
            ->willReturn($attributeMock);

        $this->updateAttributeFromFieldMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock, $attributeMock);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($this->fieldMock);

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->updateConfigMock,
            $this->updateAttributeFromFieldMock,
            $this->processCustomFieldAttributeMock,
            $allowedKeys
        );

        $subject->execute($this->fieldMock, $fieldData);
    }

    /**
     * @param array $fieldData
     * @param array $expectedDataToAdd
     * @param string[] $allowedKeys
     * @dataProvider executeWithStoreIdDataProvider
     */
    public function testExecuteWithStoreId(
        array $fieldData,
        array $expectedDataToAdd,
        array $allowedKeys
    ): void {
        $this->fieldMock->expects($this->once())->method('addData')->with($expectedDataToAdd);
        $this->fieldMock
            ->expects($this->once())
            ->method('getStoreId')
            ->willReturn(self::STORE_ID);

        $this->processCustomFieldAttributeMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->fieldMock);

        $this->fieldResourceMock->expects($this->once())->method('save')->with($this->fieldMock);
        $this->getCustomerAddressAttributeByIdMock->expects($this->never())->method('execute');
        $this->updateConfigMock->expects($this->never())->method('execute');
        $this->updateAttributeFromFieldMock->expects($this->never())->method('execute');

        $subject = new SaveField(
            $this->fieldResourceMock,
            $this->getCustomerAddressAttributeByIdMock,
            $this->updateConfigMock,
            $this->updateAttributeFromFieldMock,
            $this->processCustomFieldAttributeMock,
            $allowedKeys
        );

        $subject->execute($this->fieldMock, $fieldData);
    }

    public function executeWithDefaultStoreIdDataProvider(): array
    {
        return [
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 0,
                    'enabled'       => 1,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) Field::DEFAULT_STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'enabled'       => 1,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label']
            ],
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 1,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) Field::DEFAULT_STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 1,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label', 'sort_order']
            ],
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 0,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) Field::DEFAULT_STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'enabled'       => 0,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label', 'sort_order']
            ]
        ];
    }

    public function executeWithStoreIdDataProvider(): array
    {
        return [
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 0,
                    'enabled'       => 1,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) self::STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'enabled'       => 1,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label']
            ],
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 1,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) self::STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 1,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label', 'sort_order']
            ],
            [
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'sort_order'    => 50,
                    'enabled'       => 0,
                    'width'         => 100,
                    'required'      => 0,
                    'label'         => 'Test',
                    'store_id'      => (string) self::STORE_ID
                ],
                [
                    'attribute_id'  => self::ATTRIBUTE_ID,
                    'enabled'       => 0,
                    'label'         => 'Test',
                ],
                ['attribute_id', 'enabled', 'label', 'sort_order']
            ]
        ];
    }
}
