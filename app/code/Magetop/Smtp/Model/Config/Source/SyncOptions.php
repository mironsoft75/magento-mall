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
 * Class SyncOptions
 * @package Magetop\Smtp\Model\Config\Source
 */
class SyncOptions implements ArrayInterface
{
    const ALL      = 'all';
    const NOT_SYNC = 'not_sync';

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
                'value' => self::NOT_SYNC,
                'label' => __('New Object Only')
            ]
        ];

        return $options;
    }
}
