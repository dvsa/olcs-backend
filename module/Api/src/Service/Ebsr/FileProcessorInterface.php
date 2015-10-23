<?php
/**
 * Created by PhpStorm.
 * User: ian
 * Date: 22/10/2015
 * Time: 15:22
 */
namespace Dvsa\Olcs\Api\Service\Ebsr;

/**
 * Interface FileProcessorInterface
 * @package Dvsa\Olcs\Api\Service\Ebsr
 */
interface FileProcessorInterface
{
    /**
     * @param $identifier
     * @return string
     */
    public function fetchXmlFileNameFromDocumentStore($identifier);
}
