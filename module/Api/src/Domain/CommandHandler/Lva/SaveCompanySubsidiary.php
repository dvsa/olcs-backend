<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Lva;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Organisation\CompanySubsidiary;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class with common function to Save Company Subsidiary
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
abstract class SaveCompanySubsidiary extends AbstractCommandHandler implements TransactionedInterface
{
    /** @var  \Dvsa\Olcs\Api\Domain\Repository\CompanySubsidiary */
    protected $repo;

    /**
     * Method-Factory
     *
     * @param ServiceLocatorInterface $sl Handeler Service Manager
     *
     * @return $this
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function createService(ServiceLocatorInterface $sl)
    {
        $this->extraRepos = array_merge(['CompanySubsidiary', 'Licence', 'Application'], $this->extraRepos);

        parent::createService($sl);

        $this->repo = $this->getRepo('CompanySubsidiary');

        return $this;
    }

    /**
     * Create subsidiary
     *
     * @param TransferCmd\Lva\AbstractCreateCompanySubsidiary $command   Command
     * @param int                                             $licenceId Assign to Licence
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function create($command, $licenceId)
    {
        /** @var Entity\Licence\Licence $licence */
        $licence = $this->getRepo('Licence')->fetchById($licenceId);

        $companySubsidiary = new Entity\Organisation\CompanySubsidiary(
            $command->getName(),
            $command->getCompanyNo(),
            $licence
        );

        $this->repo->save($companySubsidiary);

        return (new Result())
            ->addId('companySubsidiary', $companySubsidiary->getId())
            ->addMessage('Company Subsidiary created');
    }

    /**
     * Update subsidiary
     *
     * @param TransferCmd\Lva\AbstractUpdateCompanySubsidiary $command Command
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function update($command)
    {
        $version = (int)$command->getVersion();

        /** @var CompanySubsidiary $companySubsidiary */
        $companySubsidiary = $this->repo->fetchUsingId(
            $command, Query::HYDRATE_OBJECT, $version
        );

        //  update record data
        $companySubsidiary->setName($command->getName());
        $companySubsidiary->setCompanyNo($command->getCompanyNo());

        $this->repo->save($companySubsidiary);

        //  define was record changed and set related status and message
        $hasChanged = ($companySubsidiary->getVersion() !== $version);
        $message = ($hasChanged ? 'updated' : 'unchanged');

        return (new Result())
            ->setFlag('hasChanged', $hasChanged)
            ->addMessage('Company Subsidiary ' . $message);
    }
}
