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
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
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
final class UpdateCompanySubsidiary extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'CompanySubsidiary';

    public function handleCommand(CommandInterface $command)
    {
        /** @var CompanySubsidiary $companySubsidiary */
        $companySubsidiary = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($companySubsidiary->getName() === $command->getName()
            && $companySubsidiary->getCompanyNo() == $command->getCompanyNo()
        ) {
            $this->result->addMessage('Company Subsidiary unchanged');
            $this->result->setFlag('hasChanged', false);
            return $this->result;
        }

        $companySubsidiary->setName($command->getName());
        $companySubsidiary->setCompanyNo($command->getCompanyNo());

        $this->getRepo()->save($companySubsidiary);

        $this->result->addMessage('Company Subsidiary updated');

        if ($this->isGranted(Permission::SELFSERVE_USER)) {
            $this->result->merge($this->createTask($command));
        }

        $this->result->setFlag('hasChanged', true);

        return $this->result;
    }

    private function createTask(Cmd $command)
    {
        $data = [
            'category' => Category::CATEGORY_APPLICATION,
            'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_SUBSIDIARY_DIGITAL,
            'description' => 'Subsidiary company updated - ' . $command->getName(),
            'licence' => $command->getLicence()
        ];

        return $this->handleSideEffect(CreateTask::create($data));
    }
}
