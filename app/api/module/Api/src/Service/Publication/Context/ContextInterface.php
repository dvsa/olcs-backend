<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

interface ContextInterface
{
    public function provide(PublicationLink $publication, \ArrayObject $context);
}