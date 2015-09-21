<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData;
use Dvsa\Olcs\Api\Entity\Publication\PublicationPoliceData as PoliceDataEntity;

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
            /**
             * @var OrganisationPersonEntity $orgPerson
             * @var PoliceDataEntity $existing
             */

            $organisationPersons = $publication->getLicence()->getOrganisation()->getOrganisationPersons();
            $existingData = $publication->getPoliceDatas();

            if (!$existingData->isEmpty()) {
                foreach ($existingData as $existing) {
                    $publication->getPoliceDatas()->removeElement($existing);
                }
            }

            if (!$organisationPersons->isEmpty()) {
                foreach ($organisationPersons as $orgPerson) {
                    $publication->getPoliceDatas()->add($this->getNewPoliceData($publication, $orgPerson));
                }
            }
        }

        return $publication;
    }

    /**
     * @param PublicationLink $publication
     * @param OrganisationPersonEntity $orgPerson
     * @return PoliceDataEntity
     */
    private function getNewPoliceData(PublicationLink $publication, OrganisationPersonEntity $orgPerson)
    {
        return new PublicationPoliceData($publication, $orgPerson->getPerson());
    }
}
