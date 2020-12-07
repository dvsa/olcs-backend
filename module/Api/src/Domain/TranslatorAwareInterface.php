<?php

namespace Dvsa\Olcs\Api\Domain;

use Laminas\I18n\Translator\TranslatorInterface;

/**
 * TranslatorAwareInterface
 */
interface TranslatorAwareInterface
{
    /**
     * @param TranslatorInterface $translator
     * @return self
     */
    public function setTranslator(TranslatorInterface $translator);

    /**
     * @return TranslatorInterface
     */
    public function getTranslator();

    /**
     * @param string $message
     * @return string
     */
    public function translate($message);
}
