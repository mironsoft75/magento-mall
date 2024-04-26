<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Cache;

use Magento\Framework\App\Cache\TypeListInterface;

class InvalidateCheckoutCache
{
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    public function __construct(TypeListInterface $cacheTypeList)
    {
        $this->cacheTypeList = $cacheTypeList;
    }

    public function execute(): void
    {
        $this->cacheTypeList->invalidate(Type::TYPE_IDENTIFIER);
    }
}
