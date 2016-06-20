<?php

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteCompanySubsidiary extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'CompanySubsidiary';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $id) {
            /** @var CompanySubsidiary $companySubsidiary */
            $companySubsidiary = $this->getRepo('CompanySubsidiary')->fetchById($id);

            if ($this->isGranted(Permission::SELFSERVE_USER)) {
                // @NOTE At the moment this would overide any existing task ID, which we don't currently use anyway
                // but this would need to change if we did need to know each task id
                $result->merge($this->createTask($command->getLicence(), $companySubsidiary->getName()));
            }

            $this->getRepo()->delete($companySubsidiary);
        }

        $result->addMessage(count($command->getIds()) . ' Company Subsidiaries removed');

        return $result;
    }

    private function createTask($licence, $name)
    {
        $data = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company deleted - ' . $name,
            'licence' => $licence
        ];

        return $this->handleSideEffect(CreateTask::create($data));
    }
}
