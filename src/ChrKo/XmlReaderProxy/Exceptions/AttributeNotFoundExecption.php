<?php

namespace ChrKo\XmlReaderProxy\Exceptions;


class AttributeNotFoundExecption extends XmlReaderExecption
{
    public function __construct($attributeName)
    {
        $message = 'Attribute ' . $attributeName . ' not found!';
        parent::__construct($message);
    }
}
