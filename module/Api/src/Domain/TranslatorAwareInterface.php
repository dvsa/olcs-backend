<?php

namespace Dvsa\Olcs\Api\Domain;

use Zend\I18n\Translator\TranslatorInterface;

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
}
