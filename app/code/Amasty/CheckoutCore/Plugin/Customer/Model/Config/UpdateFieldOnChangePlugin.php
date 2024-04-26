<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Plugin\Customer\Model\Config;

use Amasty\CheckoutCore\Model\Field\ConfigManagement\ConfigToField\ProcessDeletedConfigValue;
use Magento\Customer\Model\Config\Backend\Show\Customer as Subject;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class UpdateFieldOnChangePlugin
{
    /**
     * @var ProcessDeletedConfigValue
     */
    private $processDeletedConfigValue;

    public function __construct(ProcessDeletedConfigValue $processDeletedConfigValue)
    {
        $this->processDeletedConfigValue = $processDeletedConfigValue;
    }

    /**
     * @param Subject $configValue
     * @throws AlreadyExistsException
     * @throws NoSuchEntityException
     * @see Subject::afterDelete
     */
    public function afterAfterDelete(Subject $configValue): void
    {
        $this->processDeletedConfigValue->execute($configValue);
    }
}
