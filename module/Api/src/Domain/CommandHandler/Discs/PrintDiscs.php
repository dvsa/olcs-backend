<?php

/**
 * Print Discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Discs;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;

/**
 * Print Discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PrintDiscs extends AbstractCommandHandler implements TransactionedInterface
{
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
        $bookmark = $this->templateParams[$command->getType()]['bookmark'];

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

        $template = $this->templateParams[$command->getType()]['template'];

        $documentId = $this->generateDocument($template, $queryData, $knownValues);

        $printQueue = EnqueueFileCommand::create(
            [
                'documentId' => $documentId,
                'jobName' => $command->getType() . ' Disc List'
            ]
        );
        $printQueueResult = $this->handleSideEffect($printQueue);
        $this->result->merge($printQueueResult);
        $this->result->addMessage("Discs printed");

        return $this->result;
    }

    protected function generateDocument($template, $queryData, $knownValues)
    {
        $dtoData = [
            'template' => $template,
            'query' => $queryData,
            'knownValues' => $knownValues,
            'description' => 'Vehicle discs',
            'category' => CategoryEntity::CATEGORY_LICENSING,
            'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_DISCS,
            'isExternal' => false,
            'isScan' => false
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));

        $this->result->merge($result);

        return $result->getId('document');
    }
}
