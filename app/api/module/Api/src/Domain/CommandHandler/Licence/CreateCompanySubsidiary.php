<?php

/**
 * Create Company Subsidiary
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
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\CreateCompanySubsidiary as Cmd;

/**
 * Create Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateCompanySubsidiary extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['CompanySubsidiary'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        try {

            $this->getRepo()->beginTransaction();

            $companySubsidiary = new CompanySubsidiary($command->getName(), $command->getCompanyNo(), $licence);
            $this->getRepo('CompanySubsidiary')->save($companySubsidiary);

            $result->addId('companySubsidiary', $companySubsidiary->getId());
            $result->addMessage('Company Subsidiary created');

            if ($this->isGranted(Permission::SELFSERVE_USER)) {
                $result->merge($this->createTask($command));
            }

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
            'description' => 'Subsidiary company added - ' . $command->getName(),
            'licence' => $command->getLicence()
        ];

        return $this->getCommandHandler()->handleCommand(CreateTask::create($data));
    }
}
