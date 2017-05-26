<?php

/**
 * Generate the Organisation name, for sole traders and partnerships
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Generate the Organisation name, for sole traders and partnerships
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class GenerateName extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';
    protected $extraRepos = ['Organisation'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($command);
        if ($application->getIsVariation()) {
            throw new ValidationException(['Can only generate Orgnaisation name for a new application']);
        }

        $organisation = $application->getLicence()->getOrganisation();
        if (!$organisation->isSoleTrader() && !$organisation->isPartnership()) {
            throw new ValidationException(['Can only generate Orgnaisation name for a sole trader or partnership']);
        }

        $name = $this->generateName($organisation);
        if ($name === false) {
            $result->addMessage('Unable to generate name');
        } else {
            $organisation->setName($name);
            $this->getRepo('Organisation')->save($organisation);
            $result->addMessage('Name succesfully generated');
        }

        return $result;
    }

    /**
     * Generate the name for an organisation
     *
     * @param Organisation $organisation
     *
     * @return string|boolean false if cannot generate, otherwise the generated name
     */
    private function generateName(Organisation $organisation)
    {
        $name = false;
        if ($organisation->isSoleTrader()) {
            /* @var $firstPerson \Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson */
            $firstPerson = $organisation->getOrganisationPersons()->first();
            if ($firstPerson !== false) {
                $name = $firstPerson->getPerson()->getForename() .' '. $firstPerson->getPerson()->getFamilyName();
            }
        } else {
            // must be a partnership
            switch ($organisation->getOrganisationPersons()->count()) {
                case 0:
                    break;
                case 1:
                    $firstPerson = $organisation->getOrganisationPersons()->first()->getPerson();
                    $name = $firstPerson->getForename() .' '. $firstPerson->getFamilyName();
                    break;
                case 2:
                    $firstPerson = $organisation->getOrganisationPersons()->first()->getPerson();
                    $secondPerson = $organisation->getOrganisationPersons()->next()->getPerson();
                    $name = $firstPerson->getForename() .' '. $firstPerson->getFamilyName() .
                        ' & '. $secondPerson->getForename() .' '. $secondPerson->getFamilyName();
                    break;
                default:
                    $firstPerson = $organisation->getOrganisationPersons()->first()->getPerson();
                    $name = $firstPerson->getForename() .' '. $firstPerson->getFamilyName() .' & Partners';
                    break;
            }

        }

        return $name;
    }
}
