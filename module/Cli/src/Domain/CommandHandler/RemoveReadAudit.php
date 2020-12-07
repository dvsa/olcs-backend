<?php

/**
 * Remove Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\ReadAudit\ReadAuditRepositoryInterface;

/**
 * Remove Read Audit
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class RemoveReadAudit extends AbstractCommandHandler
{
    protected $extraRepos = [
        'ApplicationReadAudit',
        'LicenceReadAudit',
        'CasesReadAudit',
        'BusRegReadAudit',
        'TransportManagerReadAudit',
        'OrganisationReadAudit',
    ];

    protected $maxAge = '1 year';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $config = $mainServiceLocator->get('Config');

        if (isset($config['batch_config']['remove-read-audit']['max-age'])) {
            $this->maxAge = $config['batch_config']['remove-read-audit']['max-age'];
        }

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $oldestDate = date('Y-m-d', strtotime('-' . $this->maxAge));

        foreach ($this->extraRepos as $repoName) {

            /** @var ReadAuditRepositoryInterface $repo */
            $repo = $this->getRepo($repoName);

            $effected = $repo->deleteOlderThan($oldestDate);

            $this->result->addMessage($effected . ' ' . $repoName . ' records older than ' . $oldestDate . ' removed');
        }

        return $this->result;
    }
}
