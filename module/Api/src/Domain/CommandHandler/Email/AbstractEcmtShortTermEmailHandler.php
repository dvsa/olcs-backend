<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

/**
 * Abstract ECMT short term email handler
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
abstract class AbstractEcmtShortTermEmailHandler extends AbstractEmailHandler
{
    /**
     * {@inheritdoc}
     */
    protected function getTranslateToWelsh($recordObject)
    {
        return $recordObject->getLicence()->getTranslateToWelsh();
    }
}
