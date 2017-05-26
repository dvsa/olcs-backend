<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class GenerateName extends AbstractCommandHandler implements TransactionedInterface
{
    const ERR_ORG_TYPE_INVALID = 'Can only generate Organisation name for a sole trader or partnership';
    const ERR_ONLY_NEW_APP = 'Can only generate Organisation name for a new application';
    const ERR_ORG_INVALID = 'Organisation not found';
    const ERR_INVALID_DATA = 'Application or licence must be provided';

    protected $repoServiceName = 'Organisation';
    protected $extraRepos = ['Licence', 'Application'];

    /** @var  \Dvsa\Olcs\Transfer\Command\Organisation\GenerateName */
    private $command;
    /** @var  Entity\Organisation\Organisation */
    private $organisation;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\Organisation\GenerateName $command Command
     *
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->command = $command;

        if ($this->command->getApplication() !== null) {
            return $this->processApplication();
        }

        if ($this->command->getLicence() !== null) {
            return $this->processLicence();
        }

        throw new BadRequestException(self::ERR_INVALID_DATA);
    }

    /**
     * Process For Application
     *
     * @return Result
     * @throws ValidationException
     */
    private function processApplication()
    {
        /** @var Entity\Application\Application $app */
        $app = $this->getRepo('Application')->fetchById($this->command->getApplication());

        if ($app->getIsVariation()) {
            throw new ValidationException([self::ERR_ONLY_NEW_APP]);
        }

        $this->organisation = $app->getLicence()->getOrganisation();

        return $this->processCommon();
    }

    /**
     * Process licence
     *
     * @return Result
     */
    private function processLicence()
    {
        /** @var Entity\Licence\Licence $lic */
        $lic = $this->getRepo('Licence')->fetchById($this->command->getLicence());
        $this->organisation = $lic->getOrganisation();

        return $this->processCommon();
    }

    /**
     * Process common
     *
     * @return Result
     * @throws ValidationException
     */
    private function processCommon()
    {
        if (!$this->organisation instanceof Entity\Organisation\Organisation) {
            throw new ValidationException([self::ERR_ORG_INVALID]);
        }

        if (!$this->organisation->isSoleTrader() && !$this->organisation->isPartnership()) {
            throw new ValidationException([self::ERR_ORG_TYPE_INVALID]);
        }

        //  define name
        $name = $this->generateName($this->organisation);
        if ($name === null) {
            return $this->result->addMessage('Unable to generate name');
        }

        //  save new name
        $this->organisation->setName($name);
        $this->getRepo()->save($this->organisation);

        return $this->result->addMessage('Name succesfully generated');
    }

    /**
     * Generate the name for an organisation
     *
     * @param Organisation $organisation Organistion
     *
     * @return string|boolean false if cannot generate, otherwise the generated name
     */
    private function generateName(Organisation $organisation)
    {
        $orgPersons = $organisation->getOrganisationPersons();

        //  check have persons
        $cnt = $orgPersons->count();
        if ($cnt == 0) {
            return null;
        }

        //  sole traider
        /** @var Entity\Person\Person $person */
        if ($organisation->isSoleTrader()) {
            $person = $orgPersons->first()->getPerson();

            return $person->getForename() . ' ' . $person->getFamilyName();
        }

        // must be a partnership
        $person = $orgPersons->first()->getPerson();

        $name = [
            $person->getForename(),
            $person->getFamilyName(),
        ];

        if ($cnt == 2) {
            $person = $orgPersons->next()->getPerson();
            array_push($name, '&', $person->getForename(), $person->getFamilyName());
        } elseif ($cnt > 2) {
            array_push($name, '&', 'Partners');
        }

        return implode(' ', $name);
    }
}
