<?php

/**
 * Update Company Subsidiary
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
use Dvsa\Olcs\Transfer\Command\Licence\UpdateCompanySubsidiary as Cmd;

/**
 * Update Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateCompanySubsidiary extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'CompanySubsidiary';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var CompanySubsidiary $companySubsidiary */
        $companySubsidiary = $this->getRepo('CompanySubsidiary')
            ->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($companySubsidiary->getName() === $command->getName()
            && $companySubsidiary->getCompanyNo() == $command->getCompanyNo()
        ) {
            $result->addMessage('Company Subsidiary unchanged');
            $result->setFlag('hasChanged', false);
            return $result;
        }

        try {

            $this->getRepo()->beginTransaction();

            $companySubsidiary->setName($command->getName());
            $companySubsidiary->setCompanyNo($command->getCompanyNo());

            $this->getRepo()->save($companySubsidiary);

            $result->addMessage('Company Subsidiary updated');

            if ($this->isGranted(Permission::SELFSERVE_USER)) {
                $result->merge($this->createTask($command));
            }

            $result->setFlag('hasChanged', true);

            $this->getRepo()->commit();

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function createTask(Cmd $command)
    {
        $data = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company updated - ' . $command->getName(),
            'licence' => $command->getLicence()
        ];

        return $this->getCommandHandler()->handleCommand(CreateTask::create($data));
    }
}
