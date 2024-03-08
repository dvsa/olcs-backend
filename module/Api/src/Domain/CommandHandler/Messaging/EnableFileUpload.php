<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Messaging;

use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\Repository\Organisation as OrganisationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Messaging\EnableFileUpload as EnableFileUploadCommand;
use Olcs\Logging\Log\Logger;

final class EnableFileUpload extends AbstractCommandHandler implements ToggleRequiredInterface, CacheAwareInterface
{
    use ToggleAwareTrait;
    use CacheAwareTrait;

    protected $extraRepos = [OrganisationRepo::class];
    protected $toggleConfig = [FeatureToggle::MESSAGING];

    /** @var CommandInterface|EnableFileUploadCommand $command */
    public function handleCommand(CommandInterface $command): Result
    {
        $repo = $this->getRepo(OrganisationRepo::class);

        $organisation = $repo->fetchById($command->getOrganisation());
        $organisation->setIsMessagingFileUploadEnabled(true);

        $repo->save($organisation);

        try {
            $this->clearOrganisationCaches($organisation);
        } catch (\Exception $e) {
            Logger::err(
                'Cache clear by organisation failed when enabling file upload for organisation',
                [
                    'organisation_id' => $organisation->getId(),
                    'exception' => [
                        'class' => get_class($e),
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ],
                ]
            );
        }

        $result = new Result();
        $result->addId('organisation', $organisation->getId());
        $result->addMessage('File upload enabled');

        return $result;
    }
}
