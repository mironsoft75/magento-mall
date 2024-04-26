<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * @var $loggerType
     */
    protected $loggerType = Logger::INFO;
 
    /**
     * @var string $fileName
     */
    protected $fileName = '/var/log/spintowin.log';
}
