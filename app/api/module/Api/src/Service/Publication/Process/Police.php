<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData;

/**
 * Class Police
 * @package Dvsa\Olcs\Api\Service\Publication\Process
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class Police implements ProcessInterface
{
    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        //make sure we have a licence
        if ($publication->getLicence() !== null) {
            $organisationPersons = $publication->getLicence()->getOrganisation()->getOrganisationPersons();

            /**
             * @var PersonEntity $person
             * @var OrganisationPersonEntity $organisationPerson
             */
            $policeDataCollection = new ArrayCollection();

            if (!$organisationPersons->isEmpty()) {
                foreach ($organisationPersons as $organisationPerson) {
                    $person = $organisationPerson->getPerson();

                    $policeData = new PublicationPoliceData(
                        $publication,
                        $person->getBirthDate(),
                        $person->getForename(),
                        $person->getFamilyName()
                    );

                    $policeDataCollection->add($policeData);
                }
            }

            $publication->setPoliceDatas($policeDataCollection);
        }

        return $publication;
    }
}
