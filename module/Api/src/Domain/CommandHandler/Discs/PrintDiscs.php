<?php

/**
 * Print Discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Print Discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PrintDiscs extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    private $templateParams = [
        'PSV' => [
            'template' => 'PSVDiscTemplate',
            'bookmark' => 'Psv_Disc_Page'
        ],
        'Goods' => [
            'template' => 'GVDiscTemplate',
            'bookmark' => 'Disc_List'
        ]
    ];

    protected $documentService;

    protected $queryHandlerManager;

    protected $contentStore;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->documentService = $serviceLocator->getServiceLocator()->get('Document');
        $this->queryHandlerManager = $serviceLocator->getServiceLocator()->get('QueryHandlerManager');
        $this->contentStore = $serviceLocator->getServiceLocator()->get('ContentStore');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $commandResult = new Result();

        $bookmark = $this->templateParams[$command->getType()]['bookmark'];
        $filename = $this->templateParams[$command->getType()]['template'] . '.rtf';
        $template = '/templates/' . $filename;
        $discsToPrint = $command->getDiscs();
        $documentGenerator = $this->getDocumentGenerator();

        $queryData = [];
        foreach ($discsToPrint as $disc) {
            $queryData[] = $disc->getId();
        }
        $file = $this->contentStore->read($template);
        $queries = $this->documentService->getBookmarkQueries($file, $queryData);
        $result = [];

        foreach ($queries as $token => $query) {
            $list = [];
            foreach ($query as $qry) {
                try {
                    $list[] = $this->queryHandlerManager->handleQuery($qry);
                } catch (\Exception $ex) {
                    throw new \Exception('Error fetching data for bookmark: ' . $token . ': ' . $ex->getMessage());
                }
            }
            $result[$token] = $list;
        }

        $discNumber = (int) $command->getStartNumber();
        // NB the loop-by-reference here
        foreach ($result[$bookmark] as &$row) {
            $row['discNo'] = $discNumber ++;
        }

        $document = $this->documentService->populateBookmarks($file, $result);
        $storedFile = $documentGenerator->uploadGeneratedContent($document, 'documents', $filename);

        $printQueue = EnqueueFileCommand::create(
            [
                'fileIdentifier' => $storedFile->getIdentifier(),
                // @note not working for now, just migrated, will be implemented in future stories
                'options' => [PrintSchedulerInterface::OPTION_DOUBLE_SIDED],
                'jobName' => $command->getType() . ' Disc List'
            ]
        );
        $printQueueResult = $this->handleSideEffect($printQueue);
        $commandResult->merge($printQueueResult);
        $commandResult->addMessage("Discs printed");

        return $commandResult;
    }
}
