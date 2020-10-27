<?php

namespace Dvsa\Olcs\Api\Entity\ContactDetails;

use Doctrine\ORM\Mapping as ORM;

/**
 * Country Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="country",
 *    indexes={
 *        @ORM\Index(name="ix_country_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_country_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Country extends AbstractCountry
{
    const ID_AUSTRIA = 'AT';
    const ID_BELARUS = 'BY';
    const ID_BELGIUM = 'BE';
    const ID_BULGARIA = 'BG';
    const ID_CROATIA = 'HR';
    const ID_CYPRUS = 'CY';
    const ID_CZECH_REPUBLIC = 'CZ';
    const ID_DENMARK = 'DK';
    const ID_ESTONIA = 'EE';
    const ID_FINLAND = 'FI';
    const ID_FRANCE = 'FR';
    const ID_GEORGIA = 'GE';
    const ID_GERMANY = 'DE';
    const ID_GREECE = 'GR';
    const ID_HUNGARY = 'HU';
    const ID_ICELAND = 'IS';
    const ID_IRELAND = 'IE';
    const ID_ITALY = 'IT';
    const ID_KAZAKHSTAN = 'KZ';
    const ID_LATVIA = 'LV';
    const ID_LIECHTENSTEIN = 'LI';
    const ID_LITHUANIA = 'LT';
    const ID_LUXEMBOURG = 'LU';
    const ID_MALTA = 'MT';
    const ID_MOROCCO = 'MA';
    const ID_NETHERLANDS = 'NL';
    const ID_NORWAY = 'NO';
    const ID_POLAND = 'PL';
    const ID_PORTUGAL = 'PT';
    const ID_ROMANIA = 'RO';
    const ID_RUSSIA = 'RU';
    const ID_SLOVAKIA = 'SK';
    const ID_SLOVENIA = 'SI';
    const ID_SPAIN = 'ES';
    const ID_SWEDEN = 'SE';
    const ID_TUNISIA = 'TN';
    const ID_TURKEY = 'TR';
    const ID_UKRAINE = 'UA';

    /**
     * Whether this country is Morocco
     *
     * @return bool
     */
    public function isMorocco()
    {
        return $this->id == self::ID_MOROCCO;
    }
}
