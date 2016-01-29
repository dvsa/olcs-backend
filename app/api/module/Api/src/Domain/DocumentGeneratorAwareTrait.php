<?php

namespace Dvsa\Olcs\Api\Domain;

use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;

/**
 * DocumentGeneratorAwareTrait
 */
trait DocumentGeneratorAwareTrait
{
    /**
     * @var DocumentGenerator
     */
    protected $documentGenerator;

    /**
     * @param DocumentGenerator $service
     */
    public function setDocumentGenerator(DocumentGenerator $service)
    {
        $this->documentGenerator = $service;
    }

    /**
     * @return DocumentGenerator
     */
    public function getDocumentGenerator()
    {
        return $this->documentGenerator;
    }
}
