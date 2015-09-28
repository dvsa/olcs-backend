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
final class Text1 implements ProcessInterface
{
    protected $previousPublication = '(%s)';

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $application = $publication->getApplication();
        $licence = $publication->getLicence();
        $text = $licence->getLicNo() .' '. $application->getLicenceTypeShortCode();

        if ($context->offsetExists('previousPublication')) {
            $text .= "\n" . sprintf($this->previousPublication, $context->offsetGet('previousPublication'));
        }

        $publication->setText1($text);
        return $publication;
    }
}
