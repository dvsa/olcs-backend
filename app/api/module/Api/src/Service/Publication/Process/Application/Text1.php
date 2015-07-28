<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Application;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;

/**
 * Class Text1
 * @package Dvsa\Olcs\Api\Service\Publication\Process\Application
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Text1 implements ProcessInterface
{
    protected $previousPublication = '(Previous Publication:(%s))';

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $licence = $publication->getLicence();
        $text = $licence->getLicNo() . $licence->getLicenceType()->getOlbsKey();

        if ($context->offsetExists('previousPublication')) {
            $text .= ' ' . sprintf($this->previousPublication, $context->offsetGet('previousPublication'));
        }

        $publication->setText1($text);
        return $publication;
    }
}
