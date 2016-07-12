<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\Lva\SaveCompanySubsidiary;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateCompanySubsidiary extends SaveCompanySubsidiary implements AuthAwareInterface
{
    use AuthAwareTrait;

    /**
     * Command Handler
     *
     * @param \Dvsa\Olcs\Transfer\Command\Licence\CreateCompanySubsidiary $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $licenceId = $command->getLicence();

        //  create subsidiary
        $this->result = $this->create($command, $licenceId);

        //  create task
        if ($this->isGranted(Permission::SELFSERVE_USER)) {
            $data = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
                'description' => 'Subsidiary company added - ' . $command->getName(),
                'licence' => $licenceId,
            ];

            $this->result->merge(
                $this->handleSideEffect(CreateTask::create($data))
            );
        }

        return $this->result;
    }
}
