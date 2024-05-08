<?php

namespace Magetop\Smtp\Mail\Rse;

use Laminas\Mail\Header\HeaderInterface;
use Laminas\Mail\Header\GenericHeader;
use Laminas\Mail\Header\HeaderWrap;
use Laminas\Mail\Header\HeaderValue;
use Laminas\Mail\Header\Exception;

class ReturnPath implements HeaderInterface
{
    /**
     * @var string
     */
    protected $returnPath;

    public static function fromString($headerLine)
    {
        list($name, $value) = GenericHeader::splitHeaderLine($headerLine);
        $value = HeaderWrap::mimeDecodeValue($value);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'return-path') {
            throw new Exception\InvalidArgumentException('Invalid header line for Return-Path string');
        }

        $header = new static();
        $header->setPath($value);

        return $header;
    }

    public function getFieldName()
    {
        return 'Return-Path';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->returnPath;
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
        return 'Return-Path: ' . $this->getFieldValue();
    }

    /**
     * Set the message id
     *
     * @param string|null $id
     * @return MessageId
     */
    public function setPath($path)
    {
        if (empty($path)) {
            throw new Exception\InvalidArgumentException('Invalid Return-Path detected');
        }

        $path = trim($path, '<>');

        if (!HeaderValue::isValid($path) || preg_match("/[\r\n]/", $path)) {
            throw new Exception\InvalidArgumentException('Invalid ID detected');
        }

        $this->returnPath = sprintf('<%s>', $path);
        return $this;
    }
}
