<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Variation;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;

/**
 * Variation Text3
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Text3 implements ProcessInterface
{
    private $text = [];

    /**
     * @param PublicationLink      $publicationLink
     * @param ImmutableArrayObject $context
     */
    public function process(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $this->addCorrespondanceAddress($context);
        $this->addOperatingCentreText($context);
        $this->addTransportManagerText($context);
        $this->addConditionUndertakingText($context);
        $this->addUpgradeText($publicationLink);

        $publicationLink->setText3(implode("\n", $this->text));
    }

    /**
     * Add text to the
     *
     * @param string $text
     */
    private function addText($text)
    {
        if ($text) {
            $this->text[] = $text;
        }
    }

    /**
     * Add licence correspondance address
     *
     * @param ImmutableArrayObject $context
     */
    private function addCorrespondanceAddress(ImmutableArrayObject $context)
    {
        if ($context->offsetExists('licenceAddress')) {
            $this->addText($context->offsetGet('licenceAddress'));
        }
    }

    /**
     * @param ImmutableArrayObject $context
     */
    private function addOperatingCentreText(ImmutableArrayObject $context)
    {
        if ($context->offsetExists('operatingCentres') && is_array($context->offsetGet('operatingCentres'))) {
            foreach ($context->offsetGet('operatingCentres') as $textLine) {
                $this->addText($textLine);
            }
        }
    }

    /**
     * @param ImmutableArrayObject $context
     */
    private function addTransportManagerText(ImmutableArrayObject $context)
    {
        if ($context->offsetExists('transportManagers')) {
            $this->addText('Transport Manager(s): '. $context->offsetGet('transportManagers'));
        }
    }

    /**
     * @param ImmutableArrayObject $context
     */
    private function addConditionUndertakingText(ImmutableArrayObject $context)
    {
        if ($context->offsetExists('conditionUndertaking') && is_array($context->offsetGet('conditionUndertaking'))) {
            foreach ($context->offsetGet('conditionUndertaking') as $textLine) {
                $this->addText($textLine);
            }
        }
    }

    /**
     * Add Licence upgrade text
     *
     * @param PublicationLink $publicationLink
     */
    private function addUpgradeText(PublicationLink $publicationLink)
    {
        if ($publicationLink->getApplication()->isRealUpgrade()) {
            $this->addText(
                sprintf(
                    'Upgrade of Licence from %s to %s',
                    $publicationLink->getLicence()->getLicenceType()->getDescription(),
                    $publicationLink->getApplication()->getLicenceType()->getDescription()
                )
            );
        }
    }
}
