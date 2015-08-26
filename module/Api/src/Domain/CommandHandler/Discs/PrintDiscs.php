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

    public function handleCommand(CommandInterface $command)
    {
        $commandResult = new Result();

        $bookmark = $this->templateParams[$command->getType()]['bookmark'];
        $filename = $this->templateParams[$command->getType()]['template'] . '.rtf';

        $discsToPrint = $command->getDiscs();
        $queryData = [];
        foreach ($discsToPrint as $disc) {
            $queryData[] = $disc->getId();
        }

        $knownValues = [
            $bookmark => []
        ];
        $discNumber = (int) $command->getStartNumber();
        for ($i = 0; $i < count($discsToPrint); $i++) {
            $knownValues[$bookmark][$i]['discNo'] = $discNumber++;
        }

        $documentGenerator = $this->getDocumentGenerator();
        $document = $documentGenerator->generateFromTemplate(
            $this->templateParams[$command->getType()]['template'],
            $queryData,
            $knownValues
        );

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
