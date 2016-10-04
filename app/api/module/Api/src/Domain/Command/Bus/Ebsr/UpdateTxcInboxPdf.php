<?php

/**
 * Update Txc Inbox records for a bus reg id with TransXchange PDF
 */
namespace Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\AbstractIdOnlyCommand;

/**
 * Update Txc Inbox records for a bus reg id with TransXchange PDF
 */
final class UpdateTxcInboxPdf extends AbstractIdOnlyCommand
{
    protected $document;

    /**
     * Gets document id
     *
     * @return int
     */
    public function getDocument()
    {
        return $this->document;
    }
    /**
     * @var String
     * @Transfer\Filter({"name":"Zend\Filter\StringTrim"})
     * @Transfer\Validator({
     *      "name":"Zend\Validator\InArray",
     *      "options": {
     *          "haystack": {"Route","Pdf"}
     *          }
     *      })
     */
    protected $pdfType = null;

    /**
     * Whether to update the route document or the pdf document
     *
     * @return string
     */
    public function getPdfType()
    {
        return $this->pdfType;
    }
}
