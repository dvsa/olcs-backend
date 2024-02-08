<?php

/**
 * Revive an application
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Licence\Grant as GrantCmd;
use Dvsa\Olcs\Api\Domain\Command\Licence\UnderConsideration;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Olcs\Logging\Log\Logger;

/**
 * Class ReviveApplication
 *
 * Revive a previously withdrawn, NTU or refused application.
 *
 * @package Dvsa\Olcs\Api\Domain\CommandHandler\Application
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class ReviveApplication extends AbstractCommandHandler implements TransactionedInterface, CacheAwareInterface
{
    use CacheAwareTrait;

    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $licence */
        $application = $this->getRepo()->fetchById($command->getId());

        /** @var RefData $currentStatus */
        $currentStatus = $application->getStatus();

        $result = new Result();

        $licence = $application->getLicence();

        switch ($currentStatus->getId()) {
            case Application::APPLICATION_STATUS_NOT_TAKEN_UP:
                $application->setStatus(
                    $this->getRepo()
                        ->getRefdataReference(
                            Application::APPLICATION_STATUS_GRANTED
                        )
                );
                $result->merge(
                    $this->handleSideEffect(
                        GrantCmd::create(
                            [
                                'id' => $licence->getId()
                            ]
                        )
                    )
                );
                break;
            case Application::APPLICATION_STATUS_WITHDRAWN:
            case Application::APPLICATION_STATUS_REFUSED:
                $application->setStatus(
                    $this->getRepo()
                        ->getRefdataReference(
                            Application::APPLICATION_STATUS_UNDER_CONSIDERATION
                        )
                );

                if (!$application->getIsVariation()) {
                    $result->merge(
                        $this->handleSideEffect(
                            UnderConsideration::create(
                                [
                                    'id' => $licence->getId()
                                ]
                            )
                        )
                    );
                }
                break;
        }

        $this->getRepo()->save($application);

        try {
            $this->clearLicenceCaches($licence);
        } catch (\Exception $e) {
            Logger::err('Cache clear by licence failed when reviving application',
                [
                    'application_id' => $application->getId(),
                    'licence_id' => $licence->getId(),
                    'exception' => [
                        'class' => get_class($e),
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ],
                ]
            );
        }

        $result->addMessage('Application ' . $application->getId() . ' has been revived');

        return $result;
    }
}
