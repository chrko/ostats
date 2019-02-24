<?php

namespace ChrKo\XmlReaderProxy\Exceptions;


class UnknownElementException extends XmlReaderExecption {
    public function __construct($expected, $effective) {
        $message = "Wanted element '${expected}', got '${effective}'...";

        parent::__construct($message);
    }
}
