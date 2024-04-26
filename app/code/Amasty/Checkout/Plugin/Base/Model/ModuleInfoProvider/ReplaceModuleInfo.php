<?php
declare(strict_types=1);

namespace Amasty\Checkout\Plugin\Base\Model\ModuleInfoProvider;

use Amasty\Base\Model\ModuleInfoProvider;

class ReplaceModuleInfo
{
    /**
     * @param ModuleInfoProvider $subject
     * @param string $moduleCode
     * @return array|string[]
     */
    public function beforeGetModuleInfo(ModuleInfoProvider $subject, string $moduleCode)
    {
        if ($moduleCode === 'Amasty_CheckoutCore') {
            $moduleCode = 'Amasty_Checkout';
        }

        return [$moduleCode];
    }
}
