<?php

/**
 * Create VI Extract files
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Create VI Extract files
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateViExtractFiles extends AbstractCommandHandler
{
    protected $extraRepos = [
        'ViOcView',
        'ViOpView',
        'ViTnmView',
        'ViVhlView'
    ];

    protected $exportPath = null;//'/tmp';

    protected $paramMap = [
        'oc' => [
            'repo'   => 'ViOcView',
            'name'   => 'Operating Centres',
            'prefix' => 'tanopc'
        ],
        'op' => [
            'repo'   => 'ViOpView',
            'name'   => 'Operators',
            'prefix' => 'tanopo'
        ],
        'tnm' => [
            'repo'   => 'ViTnmView',
            'name'   => 'Trading Names',
            'prefix' => 'tantnm'
        ],
        'vhl' => [
            'repo'   => 'ViVhlView',
            'name'   => 'Vehicles',
            'prefix' => 'tanveh'
        ],
    ];

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $config = $mainServiceLocator->get('Config');

        if (isset($config['batch_config']['vi-extract-files']['export-path'])) {
            $this->exportPath = $config['batch_config']['vi-extract-files']['export-path'];
        }

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        if ($command->getPath()) {
            $this->exportPath = $command->getPath();
        }
        foreach ($this->paramMap as $key => $type) {
            $method = 'get' . ucfirst($key);
            $commandParam = $command->$method();
            if ($commandParam || $command->getAll()) {
                $results = $this->getRepo($type['repo'])->fetchForExport();
                $total = count($results);
                $this->result->addMessage('Found ' . $total . ' record(s) for ' . $type['name']);
                if (!$total) {
                    continue;
                }
                $content = implode(PHP_EOL, array_column($results, 'line'));
                file_put_contents(
                    $this->exportPath . '/' . $type['prefix'] . ((new \DateTime())->format('Ymd')) . '.dat',
                    $content
                );
                $this->result->addMessage($total . ' record(s) saved for ' . $type['name']);
            }
        }
        return $this->result;
    }
}
