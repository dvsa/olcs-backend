<?php


namespace Dvsa\Olcs\Api\Service\Publication\Process;


use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

interface ProcessInterface
{
    public function process(PublicationLink $publication, \ArrayObject $context);
}