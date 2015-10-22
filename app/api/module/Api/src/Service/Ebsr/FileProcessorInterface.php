<?php
/**
 * Created by PhpStorm.
 * User: ian
 * Date: 22/10/2015
 * Time: 15:22
 */
namespace Dvsa\Olcs\Api\Service\Ebsr;

interface FileProcessorInterface
{
    public function fetchXmlFileNameFromDocumentStore($identifier);
}