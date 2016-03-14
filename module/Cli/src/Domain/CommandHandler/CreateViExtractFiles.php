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
    const PHP_EOL_WIN = "\r\n";

    protected $extraRepos = [
        'ViOcView',
        'ViOpView',
        'ViTnmView',
        'ViVhlView'
    ];

    protected $exportPath = null;

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

        if (isset($config['vi_extract_files']['export_path'])) {
            $this->exportPath = $config['vi_extract_files']['export_path'];
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
                $content = implode(self::PHP_EOL_WIN, array_column($results, 'line'));
                $success = file_put_contents(
                    $this->exportPath . '/' . $type['prefix'] . ((new \DateTime())->format('Ymd')) . '.dat',
                    $content
                );
                if ($success === false) {
                    throw new \Exception(
                        'Error writing record(s) for ' . $type['name'] . ', please check the target path'
                    );
                } else {
                    $this->result->addMessage($total . ' record(s) saved for ' . $type['name']);
                }
            }
        }
        return $this->result;
    }
}
