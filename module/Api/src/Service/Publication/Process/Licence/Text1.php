<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Licence;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;

/**
 * Licence Text1
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Text1 implements ProcessInterface
{
    /**
     * @param PublicationLink $publicationLink
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        // @todo This is to be developed
        $publicationLink->setText1('To be developed');
    }
}
