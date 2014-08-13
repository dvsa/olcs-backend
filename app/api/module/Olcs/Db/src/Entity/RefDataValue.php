<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * RefDataValue Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ref_data_value",
 *    indexes={
 *        @ORM\Index(name="fk_ref_data_values_ref_data_language1_idx", columns={"language_id"}),
 *        @ORM\Index(name="fk_ref_data_values_ref_data1_idx", columns={"ref_data_id"})
 *    }
 * )
 */
class RefDataValue implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Ref data
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="ref_data_id", referencedColumnName="id")
     */
    protected $refData;

    /**
     * Language
     *
     * @var \Olcs\Db\Entity\Language
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Language", fetch="LAZY")
     * @ORM\JoinColumn(name="language_id", referencedColumnName="id")
     */
    protected $language;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column(type="string", name="value", length=100, nullable=false)
     */
    protected $value;


    /**
     * Set the ref data
     *
     * @param \Olcs\Db\Entity\RefData $refData
     * @return RefDataValue
     */
    public function setRefData($refData)
    {
        $this->refData = $refData;

        return $this;
    }

    /**
     * Get the ref data
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRefData()
    {
        return $this->refData;
    }

    /**
     * Set the language
     *
     * @param \Olcs\Db\Entity\Language $language
     * @return RefDataValue
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get the language
     *
     * @return \Olcs\Db\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set the value
     *
     * @param string $value
     * @return RefDataValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
