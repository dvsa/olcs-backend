<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview\Section;

use Laminas\I18n\Translator\TranslatorInterface;

/**
 * Abstract Review Service Services
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AbstractReviewServiceServices
{
    /** @var TranslatorInterface */
    private $translator;

    /**
     * Create service instance
     *
     * @param TranslatorInterface $translator
     *
     * @return AbstractReviewServiceServices
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Return the translator service
     *
     * @return TranslatorInterface
     */
    public function getTranslator(): TranslatorInterface
    {
        return $this->translator;
    }
}
