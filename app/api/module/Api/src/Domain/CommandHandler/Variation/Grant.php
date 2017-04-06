<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreateDiscRecords;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs as CreatePsvDiscsCmd;
use Dvsa\Olcs\Transfer\Command\Licence\VoidPsvDiscs;
use Dvsa\Olcs\Transfer\Command\Variation\Grant as Cmd;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\Command\Application\EndInterim as EndInterimCmd;
use Dvsa\Olcs\Api\Domain\Command\ConditionUndertaking\CreateSmallVehicleCondition as CreateSvConditionUndertakingCmd;

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Grant extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['GoodsDisc', 'PsvDisc'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Cmd */
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);
        $licence = $application->getLicence();

        if ($application->isPsv()) {
            $this->maybeCreateSmallVehicleCondition($application);
        }

        $result->merge($this->createSnapshot($command->getId()));

        // this must be called before anything is changed as it needs to know the differences
        // between application and licence
        if ($application->isPublishable()) {
            $result->merge($this->publishApplication($application));
            $result->merge($this->closeTexTask($application));
        }

        $this->updateStatusAndDate($application, ApplicationEntity::APPLICATION_STATUS_VALID);
        $this->getRepo()->save($application);

        if ($application->getLicenceType() !== $licence->getLicenceType()) {
            $this->updateExistingDiscs($application, $licence, $result);
        }

        $currentTotAuth = $licence->getTotAuthVehicles();

        $licence->copyInformationFromApplication($application);

        $data = $command->getArrayCopy();
        $data['currentTotAuth'] = $currentTotAuth;

        $result->merge($this->handleSideEffectAsSystemUser(CreateDiscRecords::create($data)));

        $result->merge($this->proxyCommandAsSystemUser($command, ProcessApplicationOperatingCentres::class));
        $result->merge($this->proxyCommandAsSystemUser($command, CommonGrant::class));

        if (
            $application->isGoods() && $application->isVariation() &&
            $application->getCurrentInterimStatus() === ApplicationEntity::INTERIM_STATUS_INFORCE
        ) {
            $result->merge($this->handleSideEffectAsSystemUser(EndInterimCmd::create(['id' => $application->getId()])));
        }

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = [
            'id' => $applicationId,
            'event' => CreateSnapshot::ON_GRANT
        ];

        return $this->handleSideEffectAsSystemUser(CreateSnapshot::create($data));
    }

    /**
     * @param ApplicationEntity|Licence $entity
     * @param $status
     */
    protected function updateStatusAndDate($entity, $status)
    {
        $entity->setStatus($this->getRepo()->getRefdataReference($status));
        $entity->setGrantedDate(new DateTime());
    }

    protected function updateExistingDiscs(ApplicationEntity $application, Licence $licence, Result $result)
    {
        if ($application->isGoods()) {
            $this->updateExistingGoodsDiscs($application, $licence, $result);
        } else {
            $this->updateExistingPsvDiscs($licence, $result);
        }
    }

    protected function updateExistingPsvDiscs(Licence $licence, Result $result)
    {
        $discCount = $licence->getPsvDiscsNotCeased()->count();
        if ($discCount === 0) {
            return;
        }

        $this->getRepo('PsvDisc')->ceaseDiscsForLicence($licence->getId());

        $dtoData = [
            'licence' => $licence->getId(),
            'amount' => $discCount,
            'isCopy' => 'N'
        ];

        $result->merge(
            $this->handleSideEffectAsSystemUser(CreatePsvDiscsCmd::create($dtoData))
        );
    }

    protected function updateExistingGoodsDiscs(ApplicationEntity $application, Licence $licence, Result $result)
    {
        $count = $this->getRepo('GoodsDisc')->updateExistingGoodsDiscs($application);

        $result->addMessage($count . ' Goods Disc(s) replaced');
    }

    /**
     * Close any TEX tasks on the application
     *
     * @param ApplicationEntity $application
     *
     * @return Result
     */
    protected function closeTexTask(ApplicationEntity $application)
    {
        return $this->handleSideEffectAsSystemUser(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::create(
                [
                    'id' => $application->getId(),
                ]
            )
        );
    }

    /**
     * Publish the application
     *
     * @param ApplicationEntity $application
     *
     * @return Result
     */
    protected function publishApplication(ApplicationEntity $application)
    {
        return $this->handleSideEffectAsSystemUser(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::create(
                [
                    'id' => $application->getId(),
                    'trafficArea' => $application->getTrafficArea()->getId(),
                    'publicationSection' => \Dvsa\Olcs\Api\Entity\Publication\PublicationSection::VAR_GRANTED_SECTION,
                ]
            )
        );
    }

    /**
     * Maybe create small vehicle condition
     *
     * @param ApplicationEntity $application application
     *
     * @return Result
     */
    protected function maybeCreateSmallVehicleCondition($application)
    {
        return $this->handleSideEffectAsSystemUser(
            CreateSvConditionUndertakingCmd::create(
                ['applicationId' => $application->getId()]
            )
        );
    }
}
