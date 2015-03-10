<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RefData Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity(repositoryClass="Olcs\Db\Entity\Repository\RefData")
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="ref_data",
 *    indexes={
 *        @ORM\Index(name="fk_ref_data_ref_data1_idx", columns={"parent_id"}),
 *        @ORM\Index(name="ref_data_category_id_idx", columns={"ref_data_category_id"})
 *    }
 * )
 */
class RefData implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Id32Identity;

    /**
     * Case
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Cases", mappedBy="categorys")
     */
    protected $cases;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=512, nullable=true)
     * @Gedmo\Translatable
     */
    protected $description;

    /**
     * Display order
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="display_order", nullable=true)
     */
    protected $displayOrder;

    /**
     * Impounding
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Impounding", mappedBy="impoundingLegislationTypes")
     */
    protected $impoundings;

    /**
     * Olbs key
     *
     * @var string
     *
     * @ORM\Column(type="string", name="olbs_key", length=20, nullable=true)
     */
    protected $olbsKey;

    /**
     * Opposition
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Opposition", mappedBy="grounds")
     */
    protected $oppositions;

    /**
     * Organisation
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Organisation", mappedBy="refDatas")
     */
    protected $organisations;

    /**
     * Outcome case
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Cases", mappedBy="outcomes")
     */
    protected $outcomeCases;

    /**
     * Parent
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
     */
    protected $parent;

    /**
     * Pi
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Pi", mappedBy="piTypes")
     */
    protected $pis;

    /**
     * Ref data category id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ref_data_category_id", length=32, nullable=false)
     */
    protected $refDataCategoryId;

    /**
     * Tm case decision rehab
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TmCaseDecision", mappedBy="rehabMeasures")
     */
    protected $tmCaseDecisionRehabs;

    /**
     * Tm case decision unfitness
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TmCaseDecision", mappedBy="unfitnessReasons")
     */
    protected $tmCaseDecisionUnfitnesss;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->pis = new ArrayCollection();
        $this->tmCaseDecisionRehabs = new ArrayCollection();
        $this->tmCaseDecisionUnfitnesss = new ArrayCollection();
        $this->organisations = new ArrayCollection();
        $this->oppositions = new ArrayCollection();
        $this->cases = new ArrayCollection();
        $this->outcomeCases = new ArrayCollection();
        $this->impoundings = new ArrayCollection();
    }

    /**
     * Set the case
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function setCases($cases)
    {
        $this->cases = $cases;

        return $this;
    }

    /**
     * Get the cases
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCases()
    {
        return $this->cases;
    }

    /**
     * Add a cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function addCases($cases)
    {
        if ($cases instanceof ArrayCollection) {
            $this->cases = new ArrayCollection(
                array_merge(
                    $this->cases->toArray(),
                    $cases->toArray()
                )
            );
        } elseif (!$this->cases->contains($cases)) {
            $this->cases->add($cases);
        }

        return $this;
    }

    /**
     * Remove a cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $cases
     * @return RefData
     */
    public function removeCases($cases)
    {
        if ($this->cases->contains($cases)) {
            $this->cases->removeElement($cases);
        }

        return $this;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return RefData
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the display order
     *
     * @param int $displayOrder
     * @return RefData
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get the display order
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * Set the impounding
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function setImpoundings($impoundings)
    {
        $this->impoundings = $impoundings;

        return $this;
    }

    /**
     * Get the impoundings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getImpoundings()
    {
        return $this->impoundings;
    }

    /**
     * Add a impoundings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function addImpoundings($impoundings)
    {
        if ($impoundings instanceof ArrayCollection) {
            $this->impoundings = new ArrayCollection(
                array_merge(
                    $this->impoundings->toArray(),
                    $impoundings->toArray()
                )
            );
        } elseif (!$this->impoundings->contains($impoundings)) {
            $this->impoundings->add($impoundings);
        }

        return $this;
    }

    /**
     * Remove a impoundings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $impoundings
     * @return RefData
     */
    public function removeImpoundings($impoundings)
    {
        if ($this->impoundings->contains($impoundings)) {
            $this->impoundings->removeElement($impoundings);
        }

        return $this;
    }

    /**
     * Set the olbs key
     *
     * @param string $olbsKey
     * @return RefData
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return string
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the opposition
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return RefData
     */
    public function setOppositions($oppositions)
    {
        $this->oppositions = $oppositions;

        return $this;
    }

    /**
     * Get the oppositions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOppositions()
    {
        return $this->oppositions;
    }

    /**
     * Add a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return RefData
     */
    public function addOppositions($oppositions)
    {
        if ($oppositions instanceof ArrayCollection) {
            $this->oppositions = new ArrayCollection(
                array_merge(
                    $this->oppositions->toArray(),
                    $oppositions->toArray()
                )
            );
        } elseif (!$this->oppositions->contains($oppositions)) {
            $this->oppositions->add($oppositions);
        }

        return $this;
    }

    /**
     * Remove a oppositions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $oppositions
     * @return RefData
     */
    public function removeOppositions($oppositions)
    {
        if ($this->oppositions->contains($oppositions)) {
            $this->oppositions->removeElement($oppositions);
        }

        return $this;
    }

    /**
     * Set the organisation
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisations
     * @return RefData
     */
    public function setOrganisations($organisations)
    {
        $this->organisations = $organisations;

        return $this;
    }

    /**
     * Get the organisations
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOrganisations()
    {
        return $this->organisations;
    }

    /**
     * Add a organisations
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisations
     * @return RefData
     */
    public function addOrganisations($organisations)
    {
        if ($organisations instanceof ArrayCollection) {
            $this->organisations = new ArrayCollection(
                array_merge(
                    $this->organisations->toArray(),
                    $organisations->toArray()
                )
            );
        } elseif (!$this->organisations->contains($organisations)) {
            $this->organisations->add($organisations);
        }

        return $this;
    }

    /**
     * Remove a organisations
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $organisations
     * @return RefData
     */
    public function removeOrganisations($organisations)
    {
        if ($this->organisations->contains($organisations)) {
            $this->organisations->removeElement($organisations);
        }

        return $this;
    }

    /**
     * Set the outcome case
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $outcomeCases
     * @return RefData
     */
    public function setOutcomeCases($outcomeCases)
    {
        $this->outcomeCases = $outcomeCases;

        return $this;
    }

    /**
     * Get the outcome cases
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOutcomeCases()
    {
        return $this->outcomeCases;
    }

    /**
     * Add a outcome cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $outcomeCases
     * @return RefData
     */
    public function addOutcomeCases($outcomeCases)
    {
        if ($outcomeCases instanceof ArrayCollection) {
            $this->outcomeCases = new ArrayCollection(
                array_merge(
                    $this->outcomeCases->toArray(),
                    $outcomeCases->toArray()
                )
            );
        } elseif (!$this->outcomeCases->contains($outcomeCases)) {
            $this->outcomeCases->add($outcomeCases);
        }

        return $this;
    }

    /**
     * Remove a outcome cases
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $outcomeCases
     * @return RefData
     */
    public function removeOutcomeCases($outcomeCases)
    {
        if ($this->outcomeCases->contains($outcomeCases)) {
            $this->outcomeCases->removeElement($outcomeCases);
        }

        return $this;
    }

    /**
     * Set the parent
     *
     * @param \Olcs\Db\Entity\RefData $parent
     * @return RefData
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get the parent
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the pi
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
     */
    public function setPis($pis)
    {
        $this->pis = $pis;

        return $this;
    }

    /**
     * Get the pis
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPis()
    {
        return $this->pis;
    }

    /**
     * Add a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
     */
    public function addPis($pis)
    {
        if ($pis instanceof ArrayCollection) {
            $this->pis = new ArrayCollection(
                array_merge(
                    $this->pis->toArray(),
                    $pis->toArray()
                )
            );
        } elseif (!$this->pis->contains($pis)) {
            $this->pis->add($pis);
        }

        return $this;
    }

    /**
     * Remove a pis
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $pis
     * @return RefData
     */
    public function removePis($pis)
    {
        if ($this->pis->contains($pis)) {
            $this->pis->removeElement($pis);
        }

        return $this;
    }

    /**
     * Set the ref data category id
     *
     * @param string $refDataCategoryId
     * @return RefData
     */
    public function setRefDataCategoryId($refDataCategoryId)
    {
        $this->refDataCategoryId = $refDataCategoryId;

        return $this;
    }

    /**
     * Get the ref data category id
     *
     * @return string
     */
    public function getRefDataCategoryId()
    {
        return $this->refDataCategoryId;
    }

    /**
     * Set the tm case decision rehab
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionRehabs
     * @return RefData
     */
    public function setTmCaseDecisionRehabs($tmCaseDecisionRehabs)
    {
        $this->tmCaseDecisionRehabs = $tmCaseDecisionRehabs;

        return $this;
    }

    /**
     * Get the tm case decision rehabs
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmCaseDecisionRehabs()
    {
        return $this->tmCaseDecisionRehabs;
    }

    /**
     * Add a tm case decision rehabs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionRehabs
     * @return RefData
     */
    public function addTmCaseDecisionRehabs($tmCaseDecisionRehabs)
    {
        if ($tmCaseDecisionRehabs instanceof ArrayCollection) {
            $this->tmCaseDecisionRehabs = new ArrayCollection(
                array_merge(
                    $this->tmCaseDecisionRehabs->toArray(),
                    $tmCaseDecisionRehabs->toArray()
                )
            );
        } elseif (!$this->tmCaseDecisionRehabs->contains($tmCaseDecisionRehabs)) {
            $this->tmCaseDecisionRehabs->add($tmCaseDecisionRehabs);
        }

        return $this;
    }

    /**
     * Remove a tm case decision rehabs
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionRehabs
     * @return RefData
     */
    public function removeTmCaseDecisionRehabs($tmCaseDecisionRehabs)
    {
        if ($this->tmCaseDecisionRehabs->contains($tmCaseDecisionRehabs)) {
            $this->tmCaseDecisionRehabs->removeElement($tmCaseDecisionRehabs);
        }

        return $this;
    }

    /**
     * Set the tm case decision unfitness
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionUnfitnesss
     * @return RefData
     */
    public function setTmCaseDecisionUnfitnesss($tmCaseDecisionUnfitnesss)
    {
        $this->tmCaseDecisionUnfitnesss = $tmCaseDecisionUnfitnesss;

        return $this;
    }

    /**
     * Get the tm case decision unfitnesss
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmCaseDecisionUnfitnesss()
    {
        return $this->tmCaseDecisionUnfitnesss;
    }

    /**
     * Add a tm case decision unfitnesss
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionUnfitnesss
     * @return RefData
     */
    public function addTmCaseDecisionUnfitnesss($tmCaseDecisionUnfitnesss)
    {
        if ($tmCaseDecisionUnfitnesss instanceof ArrayCollection) {
            $this->tmCaseDecisionUnfitnesss = new ArrayCollection(
                array_merge(
                    $this->tmCaseDecisionUnfitnesss->toArray(),
                    $tmCaseDecisionUnfitnesss->toArray()
                )
            );
        } elseif (!$this->tmCaseDecisionUnfitnesss->contains($tmCaseDecisionUnfitnesss)) {
            $this->tmCaseDecisionUnfitnesss->add($tmCaseDecisionUnfitnesss);
        }

        return $this;
    }

    /**
     * Remove a tm case decision unfitnesss
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmCaseDecisionUnfitnesss
     * @return RefData
     */
    public function removeTmCaseDecisionUnfitnesss($tmCaseDecisionUnfitnesss)
    {
        if ($this->tmCaseDecisionUnfitnesss->contains($tmCaseDecisionUnfitnesss)) {
            $this->tmCaseDecisionUnfitnesss->removeElement($tmCaseDecisionUnfitnesss);
        }

        return $this;
    }
}
