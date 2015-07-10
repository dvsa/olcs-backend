<?php

/**
 * Populate OrganisationPerson from Companies House API
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\OrganisationPerson;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Populate OrganisationPerson from Companies House API
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class PopulateFromCompaniesHouse extends AbstractCommandHandler implements
    TransactionedInterface,
    \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\CompaniesHouseAwareTrait;

    protected $repoServiceName = 'OrganisationPerson';
    protected $extraRepos = ['Organisation', 'Person'];

    public function handleCommand(CommandInterface $command)
    {
        $organisation = $this->getRepo('Organisation')->fetchUsingId($command);

        $result = new Result();

        $llpLtdTypes = [
            \Dvsa\Olcs\Api\Entity\Organisation\Organisation::ORG_TYPE_LLP,
            \Dvsa\Olcs\Api\Entity\Organisation\Organisation::ORG_TYPE_REGISTERED_COMPANY,
        ];

        if (in_array($organisation->getType()->getId(), $llpLtdTypes) && $organisation->getCompanyOrLlpNo()) {
            // make call to companies house service in trait
            $results = $this->getCurrentCompanyOfficers($organisation->getCompanyOrLlpNo());

            foreach ($results as $personData) {
                $newPerson = new \Dvsa\Olcs\Api\Entity\Person\Person();
                $newPerson->updatePerson(
                    $personData['forename'],
                    $personData['familyName'],
                    $this->getTitleRef($personData['title']),
                    $personData['birthDate']
                );
                $this->getRepo('Person')->save($newPerson);

                $organisationPerson = new \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson();
                $organisationPerson->setOrganisation($organisation);
                $organisationPerson->setPerson($newPerson);
                $this->getRepo('OrganisationPerson')->save($organisationPerson);
            }
            $result->addMessage('Added '. count($results) .' Person(s) to the Organisation');
        }

        return $result;
    }

    /**
     * Get a Title RefData reference for a string value
     *
     * @param string $title
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData|null
     */
    protected function getTitleRef($title)
    {
        $supportedTitles = ['dr', 'miss', 'mr', 'mrs', 'ms'];

        if (in_array(strtolower($title), $supportedTitles)) {
            return $this->getRepo()->getRefdataReference('title_'. strtolower($title));
        }

        return null;
    }
}
