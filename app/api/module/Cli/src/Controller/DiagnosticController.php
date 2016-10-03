<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\Command;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Cli\Domain\Command as CliCommand;
use Dvsa\Olcs\Cli\Domain\Query as CliQuery;
use Dvsa\Olcs\Transfer\Command as TransferCommand;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Olcs\Logging\Log\Logger;
use Zend\Http\Response;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\View\Model\ConsoleModel;

/**
 * DiagnosticController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class DiagnosticController extends AbstractConsoleController
{
    const COLOR_FAIL = 10;
    const COLOR_PASS = 12;

    /**
     * @var array
     */
    private $config;

    /**
     * Remove read audit action
     *
     * @return \Zend\View\Model\ConsoleModel
     */
    public function indexAction()
    {
        $this->openamSection();
        $this->databaseSection();
        $this->printSection();
        $this->documentStoreSection();
        $this->cpmsSection();
        $this->elasticSection();
        $this->transxchangeSection();
        $this->nysiisSection();
        $this->addressSection();

        // Connect to openam
        // Address postcode lookup
//        companies house XML
//        Companies house Rest
//        Send an email (id cli param is present
//        NR
    }

    private function getConfig()
    {
        if ($this->config === null) {
            $this->config = $this->getServiceLocator()->get('config');
        }

        return $this->config;
    }

    private function databaseSection()
    {
        $this->outputHeading('DATABASE');
        $this->getConsole()->write(
            sprintf(
                "Connect to host = '%s', dbname = '%s' : ",
                $this->getConfig()['doctrine']['connection']['orm_default']['params']['host'],
                $this->getConfig()['doctrine']['connection']['orm_default']['params']['dbname']
            )
        );

        $this->handleQuery(\Dvsa\Olcs\Transfer\Query\Application\Application::create(['id' => 7]));
        $this->outputPass();
    }

    private function printSection()
    {
        $this->outputHeading('PRINT');
        // @todo prepend http as it not in the config?
        $host = 'http://'. $this->getConfig()['print']['server'];
        $this->isReachable($host);

        // @todo if a cli parameter is present then do an actual print
    }

    private function documentStoreSection()
    {
        $this->outputHeading('DOC STORE');
        $host = $this->getConfig()['document_share']['client']['baseuri'];

        $this->isReachable($host);

        $this->getConsole()->write('Download template GV_LICENCE_GB : ');
        try {
            $this->handleQuery(\Dvsa\Olcs\Transfer\Query\Document\Download::create(
                ['identifier' => \Dvsa\Olcs\Api\Entity\Doc\Document::GV_LICENCE_GB])
            );
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail(get_class($e) .' '. $e->getMessage());
        }

        $this->getConsole()->write('Download guide Advert_Template_GB_New.pdf : ');
        try {
            $this->handleQuery(\Dvsa\Olcs\Transfer\Query\Document\DownloadGuide::create(
                ['identifier' => 'Advert_Template_GB_New.pdf'])
            );
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail(get_class($e) .' '. $e->getMessage());
        }
    }

    private function cpmsSection()
    {
        $this->outputHeading('CPMS');
        $host = 'http://'. $this->getConfig()['cpms_api']['rest_client']['options']['domain'];
        $this->isReachable($host);

        $this->getConsole()->write('Get Report List : ');
        /** @var \Dvsa\Olcs\Api\Service\CpmsV2HelperService $cpms */
        $cpms = $this->getServiceLocator()->get('CpmsHelperService');
        $response = $cpms->getReportList();
        if (isset($response['items']) && isset($response['page'])) {
            $this->outputPass();
        } else {
            $this->outputFail($response);
        }
    }

    private function elasticSection()
    {
        $this->outputHeading('Elastic');
        $host = 'http://'. $this->getConfig()['elastic_search']['host'] .':'. $this->getConfig()['elastic_search']['port'];
        $this->isReachable($host);

        $this->getConsole()->write("Serach licence index for 'smith' : ");
        /** @var \Olcs\Db\Service\Search\Search $es */
        $es = $this->getServiceLocator()->get('ElasticSearch\Search');
        $result = $es->search('smith', ['licence']);
        if (isset($result['Count']) && $result['Count'] > 1 && isset($result['Results'])) {
            $this->outputPass();
        } else {
            $this->outputFail();
        }
    }

    private function transxchangeSection()
    {
        $this->outputHeading('transexchange');
        $host = $this->getConfig()['ebsr']['transexchange_publisher']['uri'];
        $this->isReachable($host);
    }

    private function nysiisSection()
    {
        $this->outputHeading('nysiis');
        if (!isset($this->getConfig()['nysiis']['wsdl']['uri'])) {
            $this->outputFail('Not configured');
            return;
        }
        $host = $this->getConfig()['nysiis']['wsdl']['uri'];
        $this->isReachable($host);

        $this->getConsole()->write('Make request: ');

        try {
            /** @var \Dvsa\Olcs\Api\Service\Data\Nysiis $nysiisService */
            $nysiisService = $this->getServiceLocator()->get(\Dvsa\Olcs\Api\Service\Data\Nysiis::class);

            $r = $nysiisService->getNysiisSearchKeys(
                [
                    'nysiisForename' => 'John',
                    'nysiisFamilyname' => 'Smith'
                ]
            );

            var_dump($r);

            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail($e->getMessage());
        }
    }

    private function openamSection()
    {
        $this->outputHeading('openam');
        $this->getConsole()->write('Fetch system user : ');
        try {
            /** @var \Dvsa\Olcs\Api\Service\OpenAm\Client $service */
            $service = $this->getServiceLocator()->get(\Dvsa\Olcs\Api\Service\OpenAm\ClientInterface::class);

            $service->fetchUser(hash('sha256', 'system'));

            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail($e->getMessage());
        }
    }

    private function addressSection()
    {
        $this->outputHeading('address lookup');
        $postcode = 'LS9 6NF';
        $this->getConsole()->write('Fetch postcode '. $postcode .' : ');
        try {
            /** @var \Dvsa\Olcs\Address\Service\Address */
            $service = $this->getServiceLocator()->get('AddressService');
            $service->fetchByPostcode($postcode);

            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail($e->getMessage());
        }
    }

    private function isReachable($host)
    {
        $this->getConsole()->write('Connect to '. $host .' : ');
        try {
            $client = new \Zend\Http\Client($host);
            if (!$client->send()->isOk()) {
                throw new \Exception($client->getResponse()->getReasonPhrase());
            }
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFail($e->getMessage());
        }
    }

    private function outputHeading($text)
    {
        $this->getConsole()->writeLine();
        $this->getConsole()->writeLine('=== '. strtoupper($text) .' ===');
    }

    private function outputPass($text = '')
    {
        $this->getConsole()->writeLine('PASS '. $text, 11);
    }

    private function outputFail($text = '')
    {
        $this->getConsole()->writeLine('FAIL '. $text, 10);
    }

    /**
     * @param $dto
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    private function handleQuery($dto)
    {
        return $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($dto);
    }
}
