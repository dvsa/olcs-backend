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
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteCompanySubsidiary as Cmd;

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteCompanySubsidiary extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'CompanySubsidiary';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        try {

            $this->getRepo()->beginTransaction();

            foreach ($command->getIds() as $id) {
                /** @var CompanySubsidiary $companySubsidiary */
                $companySubsidiary = $this->getRepo('CompanySubsidiary')->fetchById($id);

                if ($this->isGranted(Permission::SELFSERVE_USER)) {
                    $result->merge($this->createTask($command->getLicence(), $companySubsidiary->getName()));
                }

                $this->getRepo()->delete($companySubsidiary);
            }

            $result->addMessage(count($command->getIds()) . ' Company Subsidiaries removed');

            $this->getRepo()->commit();

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function createTask($licence, $name)
    {
        $data = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company deleted - ' . $name,
            'licence' => $licence
        ];

        return $this->getCommandHandler()->handleCommand(CreateTask::create($data));
    }
}
