<?php


namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\Command\Surrender\Snapshot;

class SubmitForm extends AbstractSurrenderCommandHandler
{

    protected $extraRepos = ['Licence'];
    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = $this->handleSideEffect(\Dvsa\Olcs\Transfer\Command\Surrender\Update::create(
            [
                'id' => $command->getId(),
                'status' => Surrender::SURRENDER_STATUS_SIGNED,
                'signatureType' => $this->getRepo()->getRefdataReference(RefData::SIG_PHYSICAL_SIGNATURE)
            ]
        ));

        /**
         * @var Entity\Licence\Licence $licence
         */
        $licenceRepo = $this->getRepo('Licence');
        $licence = $licenceRepo->fetchById($command->getId());
        $licence->setStatus($this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_SURRENDER_UNDER_CONSIDERATION));
        $licenceRepo->save($licence);

        $this->result->addMessage($result);

        $this->result->merge($this->handleSideEffect(Snapshot::create(['id' => $command->getId()])));

        $this->result->merge($this->createSurrenderTask($command->getId()));

        return $this->result;
    }

    private function createSurrenderTask($licId)
    {
        $taskData = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SURRENDER,
            'description' => 'Digital surrender',
            'isClosed' => 'N',
            'urgent' => 'N',
            'licence' => $licId,
        ];

        return $this->handleSideEffect(CreateTask::create($taskData));
    }
}
