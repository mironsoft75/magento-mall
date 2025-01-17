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

namespace Magetop\Smtp\Mail;

use Closure;
use Exception;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\EmailMessage;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magetop\Smtp\Helper\Data;
use Magetop\Smtp\Mail\Rse\Mail;
use Magetop\Smtp\Model\Log;
use Magetop\Smtp\Model\LogFactory;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use Zend\Mail\Message;
use Zend_Exception;
use Laminas\Mail\Header\MessageId;
use Magetop\Smtp\Mail\Rse\SES\ConfigurationSet;

/**
 * Class Transport
 * @package Magetop\Smtp\Mail
 */
class Transport
{
    /**
     * @var int Store Id
     */
    protected $_storeId;

    /**
     * @var Mail
     */
    protected $resourceMail;

    /**
     * @var LogFactory
     */
    protected $logFactory;

    /**
     * @var Registry $registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    protected $_messageId;
    protected ConfigurationSet $configurationSet;

    /**
     * Transport constructor.
     *
     * @param Mail $resourceMail
     * @param LogFactory $logFactory
     * @param Registry $registry
     * @param Data $helper
     * @param LoggerInterface $logger
     */
    public function __construct(
        Mail $resourceMail,
        LogFactory $logFactory,
        Registry $registry,
        Data $helper,
        LoggerInterface $logger,
        MessageId $messageId,
        ConfigurationSet $configurationSet
    ) {
        $this->resourceMail = $resourceMail;
        $this->logFactory   = $logFactory;
        $this->registry     = $registry;
        $this->helper       = $helper;
        $this->logger       = $logger;
        $this->_messageId   = $messageId;
        $this->configurationSet = $configurationSet;
    }

    /**
     * @param TransportInterface $subject
     * @param Closure $proceed
     *
     * @throws MailException
     * @throws Zend_Exception
     */
    public function aroundSendMessage(
        TransportInterface $subject,
        Closure $proceed
    ) {
        $this->_storeId = $this->registry->registry('mp_smtp_store_id');
        $message        = $this->getMessage($subject);

        if ($this->resourceMail->isModuleEnable($this->_storeId) && $message) {
            if ($this->helper->versionCompare('2.2.8')) {
                $message = Message::fromString($message->getRawMessage())->setEncoding('utf-8');
            }

            if (!$this->validateBlacklist($message)) {
                $message   = $this->resourceMail->processMessage($message, $this->_storeId);
                $transport = $this->resourceMail->getTransport($this->_storeId);
                try {
                    if (!$this->resourceMail->isDeveloperMode($this->_storeId)) {
                        if ($this->helper->versionCompare('2.3.3')) {
                            $message->getHeaders()->removeHeader("Content-Disposition");
                            $headers = $message->getHeaders();
                            $messageId = $this->createMessageId('aidot.com');
                            $headers->addHeader($this->_messageId->setId($messageId));
                            $headers->addHeader($this->configurationSet->setValue('aidot-mail-track'));
                            $message->setHeaders($headers);
                        }
                        $transport->send($message);
                    }
                    if ($this->helper->versionCompare('2.2.8')) {
                        $messageTmp = $this->getMessage($subject);
                        if ($messageTmp && is_object($messageTmp)) {
                            $body = $messageTmp->getBody();
                            if (is_object($body) && $body->isMultiPart()) {
                                $message->setBody($body->getPartContent("0"));
                            }
                        }
                    }

                    $this->emailLog($message);
                } catch (Exception $e) {
                     $this->emailLog($message, false);
                    throw new MailException(new Phrase($e->getMessage()), $e);
                }
            }
        } else {
            $proceed();
        }
    }

    /**
     * @param $transport
     *
     * @return mixed|null
     */
    protected function getMessage($transport)
    {
        if ($this->helper->versionCompare('2.2.0')) {
            return $transport->getMessage();
        }

        try {
            $reflectionClass = new ReflectionClass($transport);
            $message         = $reflectionClass->getProperty('_message');
        } catch (Exception $e) {
            return null;
        }

        $message->setAccessible(true);

        return $message->getValue($transport);
    }

    /**
     * @param EmailMessage $message
     *
     * @return string
     */
    public function getRecipient($message)
    {
        $emails = [];
        if ($message->getTo()) {
            foreach ($message->getTo() as $address) {
                $emails[] = $address->getEmail();
            }
        }

        return implode(',', $emails);
    }

    /**
     * @param EmailMessage $message
     *
     * @return bool
     */
    public function validateBlacklist($message)
    {
        $result = false;
        if ($this->helper->isTestEmail()) {
            return $result;
        }

        $blacklist = $this->helper->getBlacklist();
        if ($blacklist) {
            $recipient = $this->getRecipient($message);
            $patterns  = array_unique(explode(PHP_EOL, $blacklist));
            foreach ($patterns as $pattern) {
                try {
                    if (preg_match($pattern, $recipient)) {
                        $result = true;
                        break;
                    }
                } catch (Exception $e) {
                    // Ignore validate if the pattern is error
                    continue;
                }
            }
        }

        return $result;
    }

    public function createMessageId($domain)
    {
        $time = time();

        if (isset($_SERVER['REMOTE_ADDR'])) {
            $user = $_SERVER['REMOTE_ADDR'];
        } else {
            $user = getmypid();
        }
        $rand = mt_rand();
        return sha1($time . $user . $rand) . '@'.$domain;
    }

    /**
     * Save Email Sent
     *
     * @param $message
     * @param bool $status
     */
    protected function emailLog($message, $status = true)
    {
        if ($this->resourceMail->isEnableEmailLog($this->_storeId)) {
            /** @var Log $log */
            $log = $this->logFactory->create();
            try {
                $log->saveLog($message, $status);
                if ($status) {
                    $this->saveLogIdForAbandonedCart($log);
                }
            } catch (Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
    }

    /**
     * @param Log $log
     */
    protected function saveLogIdForAbandonedCart($log)
    {
        try {
            $quote = $this->registry->registry('smtp_abandoned_cart');

            if ($quote) {
                $ids = $quote->getMpSmtpAceLogIds() ?
                    $quote->getMpSmtpAceLogIds() . ',' . $log->getId() : $log->getId();
                $quote->setMpSmtpAceSent(1)->setMpSmtpAceLogIds($ids)->save();
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
