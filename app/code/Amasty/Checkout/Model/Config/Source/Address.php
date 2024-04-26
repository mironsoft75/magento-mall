<?php
declare(strict_types=1);

namespace Amasty\Checkout\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Address implements OptionSourceInterface
{
    public const CHECKED = 'checked';
    public const UNCHECKED = 'unchecked';
    public const HIDDEN = 'hidden';

    /**
     * @return array[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::CHECKED, 'label' => __('Checked')],
            ['value' => self::UNCHECKED, 'label' => __('Unchecked')],
            ['value' => self::HIDDEN, 'label' => __('Unchecked and Hidden')],
        ];
    }
}
