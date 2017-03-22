<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\ExpireAllCommunityLicences as ExpireComLics;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Publication\Licence as PublicationLicenceCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Process Continuation Not Sought
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ProcessContinuationNotSought extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    const DOCUMENT_DESCRIPTION_GB = 'GV - Termination letter following non payment of cont fee';
    const DOCUMENT_DESCRIPTION_NI = 'GV - Termination letter following non payment of cont fee (NI)';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $discsCommand = $this->createDiscsCommand($licence);

        // Set status to CNS
        $licence->setStatus($this->getRepo()->getRefdataReference(Entity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT));

        // Set CNS date to current
        $licence->setCnsDate(new DateTime());

        $this->getRepo()->save($licence);

        $result->merge(
            $this->handleSideEffects(
                [
                    // Remove any vehicles
                    RemoveLicenceVehicle::create(['licence' => $licence->getId()]),
                    // Unlink any Transport Managers
                    DeleteTransportManagerLicence::create(['licence' => $licence->getId()]),
                    // Expire community licences that are of status 'Pending', 'Active' or 'Suspended'
                    ExpireComLics::create(['id' => $licence->getId()]),
                    // Void any discs associated to vehicles linked to the licence
                    $discsCommand,
                    // Create publication for a licence
                    PublicationLicenceCmd::create(['id' => $licence->getId()])
                ]
            )
        );

        $this->printLetter($licence);

        $result->addMessage('Licence updated');

        return $result;
    }

    private function createDiscsCommand($licence)
    {
        if ($licence->isGoods()) {
            return CeaseGoodsDiscs::create(['licence' => $licence->getId()]);
        }

        return CeasePsvDiscs::create(['licence' => $licence->getId()]);
    }

    /**
     * Print letter
     *
     * @param Entity $licence licence
     *
     * @return null
     */
    protected function printLetter($licence)
    {
        $licenceId = $licence->getId();

        $template = $licence->isNi()
            ? DocumentEntity::LICENCE_TERMINATED_CONT_FEE_NOT_PAID_NI
            : DocumentEntity::LICENCE_TERMINATED_CONT_FEE_NOT_PAID_GB;

        $description = $licence->isNi()
            ? self::DOCUMENT_DESCRIPTION_NI
            : self::DOCUMENT_DESCRIPTION_GB;

        $dtoData = [
            'template' => $template,
            'query' => [
                'licence'   => $licenceId
            ],
            'licence'       => $licenceId,
            'description'   => $description,
            'category'      => CategoryEntity::CATEGORY_LICENSING,
            'subCategory'   => CategoryEntity::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isExternal'    => false,
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));
        $this->result->merge($result);

        $printQueue = EnqueueFileCommand::create(
            [
                'documentId' => $result->getId('document'),
                'jobName'    => $description
            ]
        );
        $this->result->merge($this->handleSideEffect($printQueue));
    }
}
