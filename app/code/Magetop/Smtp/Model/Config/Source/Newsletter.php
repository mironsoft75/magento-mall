<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Smtp
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Smtp\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Newsletter
 * @package Magetop\Smtp\Model\Config\Source
 */
class Newsletter implements ArrayInterface
{
    const ALL        = 'all';
    const SUBSCRIBED = 'subscribed';

    /**
     * to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::ALL,
                'label' => __('All')
            ],
            [
                'value' => self::SUBSCRIBED,
                'label' => __('Only Subscribed')
            ]
        ];

        return $options;
    }
}
