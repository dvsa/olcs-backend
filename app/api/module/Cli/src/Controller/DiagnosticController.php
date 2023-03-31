<?php

namespace Dvsa\Olcs\Cli\Controller;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Query\Diagnostics\CheckFkIntegrity;
use Dvsa\Olcs\Api\Domain\Query\Diagnostics\GenerateCheckFkIntegritySql;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Service\CpmsV2HelperService;
use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\Api\Service\Nysiis\NysiisRestClient;
use Dvsa\Olcs\Email\Service\Imap;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Olcs\Db\Service\Search\Search;
use Laminas\Console\Response;
use Laminas\Mvc\Controller\AbstractConsoleController;
use Dvsa\Olcs\Address\Service\Address;
use Laminas\Http\Client;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Transfer\Query\Document\DownloadGuide;
use Dvsa\Olcs\Transfer\Query\Document\Download;
use Dvsa\Olcs\Transfer\Query\ContactDetail\CountryList;
use Dvsa\Olcs\Email\Domain\Command\SendEmail;

/**
 * DiagnosticController
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class DiagnosticController extends AbstractConsoleController
{
    private const COLOR_FAIL = 10;
    private const COLOR_PASS = 11;
    private const COLOR_SKIP = 12;
    private const TEMPLATE_TO_DOWNLOAD = 'GV_LICENCE_GB';
    private const TEMPLATE_TO_DOWNLOAD_ID = Document::GV_LICENCE_GB;
    private const GUIDE_TO_DOWNLOAD = 'Advert_Template_GB_New.pdf';
    private const SYSTEM_USER_NAME = 'usr291';
    private const POSTCODE_TO_FETCH = 'LS9 6NF';
    private const LICENCE_SEARCH = 'smith';
    private const NYSIIS_FORENAME = 'John';
    private const NYSIIS_FAMILYNAME = 'Smith';
    private const COMPANIES_HOUSE_SEARCH_VALUE = 'next';
    private const COMPANIES_HOUSE_SEARCH_NUMBER = '02275780';
    private const EMAIL_ADDRESS_FOR_TEST_EMAIL = 'terry.valtech@gmail.com';
    private const SUBJECT_FOR_TEST_EMAIL = 'test email';
    private const BODY_FOR_TEST_EMAIL = 'test email sent from the diagnostic command';
    private const NAME_FROM_FOR_TEST_EMAIL = 'System';
    private const EMAIL_FROM_FOR_TEST_EMAIL = 'terry.valtech@gmail.com';
    private const MAILBOX_ID = 'inspection_request';
    private const XML_EXAMPLE = '<?xml version="1.0" encoding="UTF-8"?><foo></foo>';

    /**
     * @var array
     */
    private $config;

    private $configKeys = [
        'CPMS' => 'cpms_api->rest_client->options->domain',
        'DOCUMENT_SHARE' => 'document_share->client->baseuri',
        'DOCTRINE_HOST' => 'doctrine->connection->orm_default->params->host',
        'DOCTRINE_PORT' => 'doctrine->connection->orm_default->params->port',
        'PRINT' => 'print->server',
        'ELASTIC_HOST' => 'elastic_search->host',
        'ELASTIC_PORT' => 'elastic_search->port',
        'TRANSXCHANGE' => 'ebsr->transexchange_publisher->uri',
        'NYSIIS' => 'nysiis->wsdl->uri',
        'CH_XML_USERID' => 'companies_house_credentials->userId',
        'CH_XML_PASSWORD' => 'companies_house_credentials->password',
        'CH_REST_URI' => 'companies_house->client->baseuri',
        'CH_REST_USERNAME' => 'companies_house->auth->username',
        'EMAIL_CLIENT_SS' => 'email->selfserve_uri',
        'EMAIL_CLIENT_INT' => 'email->internal_uri',
        'IR_MAILBOX_HOST' => 'mailboxes->inspection_request->host',
        'IR_MAILBOX_PORT' => 'mailboxes->inspection_request->port',
        'NR_URI' => 'nr->inr_service->uri',
        'NR_REPUTE_URI' => 'nr->repute_url->uri',
    ];

    private $sections = [
        'database' => 'Database',
        'print' => 'Print',
        'documentStore' => 'Document Store',
        'cpms' => 'CPMS',
        'elastic' => 'Elastic',
        'transxchange' => 'Transxchange',
        'address' => 'Address Lookup',
        'companiesHouseRest' => 'Companies House REST API',
        'sendEmail' => 'Send email',
        'checkMailbox' => 'Check inspection request mailbox',
        'nr' => 'NR'
    ];

    private CpmsV2HelperService $cpmsHelperService;

    private Search $elasticSearch;

    private NysiisRestClient $nysiisService;

    private Address $addressService;

    private \Dvsa\Olcs\CompaniesHouse\Service\Client $companiesHouseService;

    private Imap $imapService;

    protected InrClient $inrService;

    private QueryHandlerManager $queryHandlerManager;

    private CommandHandlerManager $commandHandlerManager;

    public function __construct(
        array $config,
        CpmsV2HelperService $cpmsHelperService,
        Search $elasticSearch,
        $addressService,
        \Dvsa\Olcs\CompaniesHouse\Service\Client $companiesHouseService,
        Imap $imapService,
        InrClient $nrService,
        QueryHandlerManager $queryHandlerManager,
        CommandHandlerManager $commandHandlerManager
    ) {
        $this->config = $config;
        $this->cpmsHelperService = $cpmsHelperService;
        $this->elasticSearch = $elasticSearch;
        $this->addressService = $addressService;
        $this->companiesHouseService = $companiesHouseService;
        $this->imapService = $imapService;
        $this->inrService = $nrService;
        $this->queryHandlerManager = $queryHandlerManager;
        $this->commandHandlerManager = $commandHandlerManager;
    }

    /**
     * Index action
     *
     * @return \Laminas\View\Model\ConsoleModel
     */
    public function indexAction()
    {
        $skipSections = explode(',', $this->params('skip', ''));
        foreach ($this->sections as $section => $header) {
            $this->outputHeading($header);
            if (!in_array($section, $skipSections)) {
                $this->{$section . 'Section'}();
            } else {
                $this->outputSkip($section);
            }
        }
    }

    public function checkFkIntegrityAction()
    {
        $result = $this->handleQuery(CheckFkIntegrity::create([]));
        $response = new Response();
        if ($result['fk-constraint-violation-counts']) {
            $response->setContent(json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL);
            $response->setErrorLevel(1);
        } else {
            $response->setContent("No fk constraint violations found" . PHP_EOL);
        }
        return $response;
    }

    public function checkFkIntegritySqlAction()
    {
        $response = new Response();
        $response->setContent(
            join(PHP_EOL, $this->handleQuery(GenerateCheckFkIntegritySql::create([]))['queries']) . PHP_EOL
        );
        return $response;
    }

    /**
     * Database section
     *
     * @return void
     */
    private function databaseSection()
    {
        $host = $this->getValueFromConfig('DOCTRINE_HOST');
        $port = $this->getValueFromConfig('DOCTRINE_PORT');
        if ($host === false || $port === false) {
            return;
        }

        $this->outputMessage(sprintf("Connect to host = '%s', dbname = '%s' : ", $host, $port));

        try {
            $this->handleQuery(CountryList::create([]));
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Print section
     *
     * @return void
     */
    private function printSection()
    {
        $host = $this->getValueFromConfig('PRINT');
        if ($host === false) {
            return;
        }

        $this->isReachable($host);
    }

    /**
     * Document store section
     *
     * @return void
     */
    private function documentStoreSection()
    {
        $host = $this->getValueFromConfig('DOCUMENT_SHARE');
        if ($host === false) {
            return;
        }

        if (!$this->isReachable($host)) {
            return;
        }

        $this->outputMessage('Download template ' . self::TEMPLATE_TO_DOWNLOAD . ' : ');
        try {
            $this->handleQuery(Download::create(['identifier' => self::TEMPLATE_TO_DOWNLOAD_ID]));
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }

        $this->outputMessage('Download guide ' . self::GUIDE_TO_DOWNLOAD . ' : ');
        try {
            $this->handleQuery(DownloadGuide::create(['identifier' => self::GUIDE_TO_DOWNLOAD]));
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * CPMS section
     *
     * @return void
     */
    private function cpmsSection()
    {
        $host = $this->getValueFromConfig('CPMS');
        if ($host === false) {
            return;
        }

        if (!$this->isReachable($host)) {
            return;
        }

        $this->outputMessage('Get Report List : ');

        try {
            $response = $this->cpmsHelperService->getReportList();
            if (isset($response['items']) && isset($response['page'])) {
                $this->outputPass();
            } else {
                $this->outputFail($response);
            }
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Elastic section
     *
     * @return void
     */
    private function elasticSection()
    {
        $host = $this->getValueFromConfig('ELASTIC_HOST');
        $port = $this->getValueFromConfig('ELASTIC_PORT');
        if ($host === false || $port === false) {
            return;
        }

        if (!$this->isReachable($host, $port)) {
            return;
        }

        $this->outputMessage("Serach licence index for '" . self::LICENCE_SEARCH . "' : ");

        $result = $this->elasticSearch->search(self::LICENCE_SEARCH, ['licence']);

        if (isset($result['Count']) && $result['Count'] > 1 && isset($result['Results'])) {
            $this->outputPass();
        } else {
            $this->outputFail();
        }
    }

    /**
     * Transxchange section
     *
     * @return void
     */
    private function transxchangeSection()
    {
        $host = $this->getValueFromConfig('TRANSXCHANGE');
        if ($host === false) {
            return;
        }
        $this->isReachable($host);
    }

    /**
     * NYSIIS section
     *
     * @return void
     */
    private function nysiisSection()
    {
        // @todo: need to test and fix if needed on different envinronments, unable to test locally

        $host = $this->getValueFromConfig('NYSIIS');
        if ($host === false) {
            return;
        }

        if (!$this->isReachable($host)) {
            return;
        }

        $this->outputMessage('Make request: ');
        try {
            $this->nysiisService->getNysiisSearchKeys(
                [
                    'nysiisForename' => self::NYSIIS_FORENAME,
                    'nysiisFamilyname' => self::NYSIIS_FAMILYNAME
                ]
            );

            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Address section
     *
     * @return void
     */
    private function addressSection()
    {
        $this->outputMessage('Fetch postcode ' . self::POSTCODE_TO_FETCH . ' : ');
        try {
            $address = $this->addressService->fetchByPostcode(self::POSTCODE_TO_FETCH);
            if (count($address) === 0) {
                $this->outputFail('No results for ' . self::POSTCODE_TO_FETCH);
            } else {
                $this->outputPass();
            }
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Companies house REST section
     *
     * @return void
     */
    private function companiesHouseRestSection()
    {
        if (
            $this->getValueFromConfig('CH_REST_URI') === false
            || $this->getValueFromConfig('CH_REST_USERNAME') === false
        ) {
            return;
        }

        try {
            $this->outputMessage('Fetch company by number ' . self::COMPANIES_HOUSE_SEARCH_NUMBER . ' : ');
            $result = $this->companiesHouseService->getCompanyProfile(self::COMPANIES_HOUSE_SEARCH_NUMBER, true);
            if (!is_array($result) || count($result) === 0) {
                $this->outputFail('No results found for search term: ' . self::COMPANIES_HOUSE_SEARCH_NAME);
            } else {
                $this->outputPass();
            }
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Send email section
     *
     * @return void
     */
    private function sendEmailSection()
    {
        // @todo: need to test and fix if needed on different envinronments, unable to test locally

        $ss = $this->getValueFromConfig('EMAIL_CLIENT_SS');
        $int = $this->getValueFromConfig('EMAIL_CLIENT_INT');
        if ($ss === false || $int === false) {
            return;
        }

        $toEmail = $this->params('email', null) === null
            ? self::EMAIL_ADDRESS_FOR_TEST_EMAIL
            : $this->params('email', null);

        try {
            $this->outputMessage('Send email to ' . $toEmail . ' : ');
            $data = [
                'fromName' => self::NAME_FROM_FOR_TEST_EMAIL,
                'fromEmail' => self::EMAIL_ADDRESS_FOR_TEST_EMAIL,
                'to' => $toEmail,
                'subject' => self::SUBJECT_FOR_TEST_EMAIL,
                'plainBody' => self::BODY_FOR_TEST_EMAIL,
                'htmlBody' => self::BODY_FOR_TEST_EMAIL,
                'locale' => 'en_GB',
            ];
            $cmd = SendEmail::create($data);
            $result = $this->handleCommand($cmd, false);
            $messages = $result->getMessages();
            if (is_array($messages) && count($messages) === 1 || $messages[0] === 'Email sent') {
                $this->outputPass();
            } else {
                $this->outputFail();
            }
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Check inspection request mailbox section
     *
     * @return void
     */
    private function checkMailboxSection()
    {
        // @todo: need to test and fix if needed on different envinronments, unable to test locally

        $host = $this->getValueFromConfig('IR_MAILBOX_HOST');
        $port = $this->getValueFromConfig('IR_MAILBOX_PORT');
        if ($host === false || $port === false) {
            return;
        }

        try {
            $this->outputMessage('Connecting to ' . self::MAILBOX_ID . ' mailbox : ');

            $this->imapService->connect(self::MAILBOX_ID);
            $messages = $this->imapService->getMessages();
            $total = count($messages);
            $this->outputPass(' (found ' . $total . 'messages)');
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * NR section
     *
     * @return void
     */
    private function nrSection()
    {
        // @todo: need to test and fix if needed on different envinronments, unable to test locally

        $nrUri = $this->getValueFromConfig('NR_URI');
        $nrReputeUri = $this->getValueFromConfig('NR_REPUTE_URI');

        if ($nrUri === false || $nrReputeUri === false) {
            return;
        }

        try {
            $this->outputMessage('Sending empty request to NR service : ');

            $responseCode = $this->inrService->makeRequest(self::XML_EXAMPLE);
            $this->outputPass(' response code was ' . $responseCode);
        } catch (\Exception $e) {
            $this->outputFailEx($e);
        }
    }

    /**
     * Is reachable
     *
     * @param string $host host
     * @param string $port port
     *
     * @throws \Exception
     *
     * @return bool
     */
    private function isReachable($host, $port = null)
    {
        if (substr($host, 0, 7) !== 'http://' && substr($host, 0, 8) !== 'https://') {
            $host = 'http://' . $host;
        }
        $this->outputMessage('Connect to ' . $host . ' : ');

        try {
            $client = new Client($host . ($port ? ':' . $port : ''));

            if (!$client->send()->isOk()) {
                throw new \Exception($client->getResponse()->getReasonPhrase());
            }
            $this->outputPass();
        } catch (\Exception $e) {
            $this->outputFailEx($e);
            return false;
        }
        return true;
    }

    /**
     * Output heading
     *
     * @param string $text text
     *
     * @return void
     */
    private function outputHeading($text)
    {
        $this->getConsole()->writeLine();
        $this->getConsole()->writeLine('====== ' . $text . ' ======');
    }

    /**
     * Output pass
     *
     * @param string $text text
     *
     * @return void
     */
    private function outputPass($text = '')
    {
        $this->getConsole()->writeLine('PASS ' . $text, self::COLOR_PASS);
    }

    /**
     * Output fail
     *
     * @param string $text text
     *
     * @return void
     */
    private function outputFail($text = '')
    {
        $this->getConsole()->writeLine('FAIL' . PHP_EOL . $text, self::COLOR_FAIL);
    }

    /**
     * Output fail (from catch block)
     *
     * @param \Exception $e exception
     *
     * @return void
     */
    private function outputFailEx(\Exception $e)
    {
        $this->getConsole()->writeLine('FAIL' . PHP_EOL . get_class($e) . ' ' . $e->getMessage(), self::COLOR_FAIL);
    }

    /**
     * Output skip
     *
     * @param string $section section
     *
     * @return void
     */
    private function outputSkip($section = '')
    {
        $this->getConsole()->writeLine('SKIP ' . $section . ' section', self::COLOR_SKIP);
    }

    /**
     * Output message
     *
     * @param string $text text
     *
     * @return void
     */
    private function outputMessage($text = '')
    {
        $this->getConsole()->write($text);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $dto dto
     *
     * @return Result
     */
    private function handleQuery($dto)
    {
        return $this->queryHandlerManager->handleQuery($dto);
    }

    /**
     * Handle command
     *
     * @param CommandInterface $dto dto
     * @param bool             $shouldValidate should we validate command
     *
     * @return Result
     */
    private function handleCommand($dto, $shouldValidate = true)
    {
        return $this->commandHandlerManager->handleCommand($dto, $shouldValidate);
    }

    /**
     * Get value from config
     *
     * @param string $type type
     *
     * @return array|bool
     */
    private function getValueFromConfig($type = null)
    {
        if (!$type) {
            return false;
        }
        $path = $this->configKeys[$type];

        $keys = explode('->', $path);
        $value = $this->config;

        foreach ($keys as $key) {
            if (!isset($value[$key])) {
                $this->outputFail($type . ' not configured: ' . $key . ' key not found.');
                return false;
            }
            $value = $value[$key];
        }

        return $value;
    }
}
