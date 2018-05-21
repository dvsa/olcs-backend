<?php
/**
 * Created by PhpStorm.
 * User: parthvyas
 * Date: 17/05/2018
 * Time: 13:37
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence as TmlEntity;
use Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence as TmlRepo;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TransportManagerLicence\Delete as DeleteDto;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;


class DeleteUpdateOptOutTmLetter extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerLicence';

    /**
     * handle command
     *
     * @param CommandInterface|DeleteDto $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var $tmlRepo TmlRepo
         * @var $tmlEntity TmlEntity
         */
        $result = new Result();
        $tmlRepo = $this->getRepo();

        //we'll only ever have one record at once currently
        foreach ($command->getIds() as $tmlId) {
            $tmlEntity = $tmlRepo->fetchById($tmlId);

            $licence = $tmlEntity->getLicence();
            $tmId = $tmlEntity->getTransportManager()->getId();

            $last = ($licence->getTmLicences()->count() === 1 ? true : false);

            $tmlRepo->delete($tmlEntity);
            $result->addMessage("Transport manager licence {$tmlId} deleted");

            $result->merge(
                $this->handleSideEffect(
                    $this->createTaskSideEffect($licence->getId(), $tmId, $last)
                )
            );
        }

        return $result;
    }

    /**
     * Creates a command for task creation
     *
     * @param int  $licenceId licence id
     * @param int  $tmId      tm id
     * @param bool $last      whether this is the last tm on the licence
     *
     * @return CreateTask
     */
    private function createTaskSideEffect($licenceId, $tmId, $last)
    {
        $params = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => SubCategory::TM_SUB_CATEGORY_TM1_REMOVAL,
            'description' => $last ? TmlEntity::DESC_TM_REMOVED_LAST : TmlEntity::DESC_TM_REMOVED,
            'licence' => $licenceId,
            'transportManager' => $tmId,
            'urgent' => $last ? 'Y' : 'N'
        ];

        return CreateTask::create($params);
    }
}

