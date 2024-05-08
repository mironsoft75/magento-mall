<?php

namespace Magetop\Smtp\Mail\Rse\SES;

use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Header\GenericHeader;
use Laminas\Mail\Header\HeaderWrap;
use Laminas\Mail\Header\HeaderValue;
use Laminas\Mail\Header\Exception;

class ConfigurationSet implements HeaderInterface
{
    /**
     * @var string
     */
    protected $configurationSet;

    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);
        $value = HeaderWrap::mimeDecodeValue($value);

        // check to ensure proper header type for this factory
        if (strtoupper($name) !== 'X-SES-CONFIGURATION-SET') {
            throw new Exception\InvalidArgumentException('Invalid header line for X-SES-CONFIGURATION-SET string');
        }

        $header = new static();
        $header->setValue($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'X-SES-CONFIGURATION-SET';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->configurationSet;
    }

    public function setEncoding($encoding)
    {
        // This header must be always in US-ASCII
        return $this;
    }

    public function getEncoding()
    {
        return 'ASCII';
    }

    public function toString()
    {
        return 'X-SES-CONFIGURATION-SET: ' . $this->getFieldValue();
    }

    /**
     * Set the message id
     *
     * @param string|null $id
     * @return MessageId
     */
    public function setValue($value)
    {
        if (empty($value)) {
            throw new Exception\InvalidArgumentException('Invalid X-SES-CONFIGURATION-SET detected');
        }

        $value = trim($value, '<>');

        if (!HeaderValue::isValid($value) || preg_match("/[\r\n]/", $value)) {
            throw new Exception\InvalidArgumentException('Invalid ID detected');
        }

        $this->configurationSet = $value;
        return $this;
    }
}
