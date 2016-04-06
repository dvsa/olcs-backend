<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule as LicenceStatusRuleEntity;

/**
 * Class DeleteLicenceStatusRule
 *
 * Create a licence status rule.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule
 */
final class DeleteLicenceStatusRule extends AbstractCommandHandler
{
    protected $repoServiceName = 'LicenceStatusRule';

    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var LicenceStatusRuleEntity $licenceStatusRule */
        $licenceStatusRule = $this->getRepo()->fetchById($command->getId());

        $licence = $licenceStatusRule->getLicence();

        $this->getRepo()->delete(
            $licenceStatusRule
        );

        $result->addId('licence-status-rule', $command->getId());
        $result->addMessage('Licence status rule deleted.');

        // remove reasons
        $licence->setReasons(new ArrayCollection());

        $this->getRepo('Licence')->save($licence);

        return $result;
    }
}
