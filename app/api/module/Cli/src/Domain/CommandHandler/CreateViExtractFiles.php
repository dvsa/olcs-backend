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
            'prefix' => 'tanopc',
            'params' => [
                'ocId'
            ],
            'method' => 'clearOcViIndicators'
        ],
        'op' => [
            'repo'   => 'ViOpView',
            'name'   => 'Operators',
            'prefix' => 'tanopo',
            'params' => [
                'licId'
            ],
            'method' => 'clearLicencesViIndicators'
        ],
        'tnm' => [
            'repo'   => 'ViTnmView',
            'name'   => 'Trading Names',
            'prefix' => 'tantnm',
            'params' => [
                'tradingNameId'
            ],
            'method' => 'clearTradingNamesViIndicators'
        ],
        'vhl' => [
            'repo'   => 'ViVhlView',
            'name'   => 'Vehicles',
            'prefix' => 'tanveh',
            'params' => [
                'vhlId',
                'licId'
            ],
            'method' => 'clearLicenceVehiclesViIndicators'
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
        foreach ($this->paramMap as $key => $settings) {
            $method = 'get' . ucfirst($key);
            $commandParam = $command->$method();
            $repo = $this->getRepo($settings['repo']);
            if ($commandParam || $command->getAll()) {
                $results = $repo->fetchForExport();
                $total = count($results);
                $this->result->addMessage('Found ' . $total . ' record(s) for ' . $settings['name']);
                if (!$total) {
                    continue;
                }
                $content = implode(self::PHP_EOL_WIN, array_column($results, 'line'));
                $success = file_put_contents(
                    $this->exportPath . '/' . $settings['prefix'] . ((new \DateTime())->format('Ymd')) . '.dat',
                    $content
                );
                if ($success === false) {
                    throw new \Exception(
                        'Error writing record(s) for ' . $settings['name'] . ', please check the target path'
                    );
                }
                $this->clearViFlags($repo, $results, $key);
                $this->result->addMessage($total . ' record(s) saved for ' . $settings['name']);
                $this->result->addMessage('VI flags cleared');
            }
        }
        return $this->result;
    }

    /**
     * Clear VI flags
     *
     * @param mixed $repo
     * @param array $results
     * @param string $key
     */
    protected function clearViFlags($repo, $results, $key)
    {
        $params = [];
        foreach ($results as $result) {
            $elements = [];
            foreach ($this->paramMap[$key]['params'] as $paramName) {
                $elements[$paramName] = $result[$paramName];
            }
            $params[] = $elements;
        }
        $repo->{$this->paramMap[$key]['method']}($params);
    }
}
