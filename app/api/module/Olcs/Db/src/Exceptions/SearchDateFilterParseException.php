<?php


namespace Olcs\Db\Exceptions;


use Throwable;

class SearchDateFilterParseException extends \Exception
{

    private $dateField;

    /**
     * @return mixed
     */
    public function getDateField()
    {
        return $this->dateField;
    }

    /**
     * @param mixed $dateField
     */
    public function setDateField($dateField)
    {
        $this->dateField = $dateField;
    }


    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}