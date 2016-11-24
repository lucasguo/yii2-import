<?php

namespace lucasguo\import\exceptions;

/**
 * InvalidParamException represents an exception caused by passing a invalid file to Importer consumer.
 */
class InvalidFileException extends \BadMethodCallException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Invalid File';
    }
}