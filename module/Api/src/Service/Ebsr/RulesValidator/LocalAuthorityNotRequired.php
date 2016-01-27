<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\RulesValidator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LaEntity;

/**
 * Class LocalAuthorityNotRequired
 * @package Dvsa\Olcs\Api\Service\Ebsr\RulesValidator
 */
class LocalAuthorityNotRequired extends AbstractValidator
{
    const LA_NOT_REQUIRED_ERROR = 'la-not-required-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::LA_NOT_REQUIRED_ERROR => 'According to the stops, these Local Authorities don\'t need copying: %value%'
    ];

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @return bool
     */
    public function isValid($value, $context = [])
    {
        /**
         * @var LaEntity $la
         * @var ArrayCollection $localAuthorities
         * @var ArrayCollection $naptanLocalAuthorities
         */
        $localAuthorities = $value['localAuthoritys']; //grammar mismatch result of entity array collection name
        $naptanLocalAuthorities = $value['naptanAuthorities'];

        $notRequired = [];

        foreach ($localAuthorities as $la) {
            if (!$naptanLocalAuthorities->contains($la)) {
                $notRequired[] = $la->getDescription();
            }
        }

        if (!empty($notRequired)) {
            $this->error(self::LA_NOT_REQUIRED_ERROR, implode(', ', $notRequired));
            return false;
        }

        return true;
    }
}
