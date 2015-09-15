<?php

namespace Dvsa\Olcs\Api\Domain;

use Zend\I18n\Translator\TranslatorInterface;

/**
 * Translator Aware
 */
trait TranslatorAwareTrait
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param TranslatorInterface $service
     */
    public function setTranslator(TranslatorInterface $service)
    {
        $this->translator = $service;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param string
     * @return string
     */
    public function translate($message)
    {
        return $this->getTranslator()->translate($message);
    }
}
