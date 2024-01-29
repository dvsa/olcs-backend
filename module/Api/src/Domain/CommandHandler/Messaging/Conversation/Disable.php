<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging\Conversation;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\Conversation\Disable as DisableCommand;

/**
 * Disable conversations
 *
 * @author Wade Womersley <wade.womersley@dvsa.org.uk>
 */
final class Disable extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $extraRepos = [OrganisationRepo::class];
    protected $toggleConfig = [FeatureToggle::MESSAGING];

    /** @var CommandInterface|DisableCommand $command */
    public function handleCommand(CommandInterface $command): Result
    {
        $repo = $this->getRepo(OrganisationRepo::class);

        $organisation = $repo->fetchById($command->getOrganisation());
        $organisation->setIsMessagingDisabled(true);

        $repo->save($organisation);

        $result = new Result();
        $result->addId('organisation', $organisation->getId());
        $result->addMessage('Messaging disabled');

        return $result;
    }
}
