<?php

/**
 * Cpid Organisation Export
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Cli\Service\Queue\Consumer;

use Dvsa\Olcs\Api\Domain\CommandHandler\CommandHandlerInterface;
use Dvsa\Olcs\Api\Domain\Repository\Organisation;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Api\Domain\Command\Queue\Complete as CompleteCmd;
use Dvsa\Olcs\Api\Domain\Command\Queue\Failed as FailedCmd;
use Dvsa\Olcs\Transfer\Command\Document\Upload;

/**
 * Cpid Organisation Export
 *
 * @author Josh Curtis <josh@josh-curtis.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CpidOrganisationExport implements MessageConsumerInterface
{
    /**
     * @var Organisation
     */
    private $organisationRepo;

    /**
     * @var CommandHandlerInterface
     */
    private $commandHandler;

    public function __construct(Organisation $organisation, \Dvsa\Olcs\Api\Domain\CommandHandlerManager $commandHandler)
    {
        $this->organisationRepo = $organisation;
        $this->commandHandler = $commandHandler;
    }

    public function processMessage(QueueEntity $item)
    {
        $options = (array)json_decode($item->getOptions());

        $iterableResult = $this->organisationRepo->fetchAllByStatusForCpidExport($options['status']);

        $rows = [];

        while (($row = $iterableResult->next()) !== false) {
            $rows[] = $this->arrayToCsv($row[key($row)]);
        }

        $dtoData = [
            'content' => base64_encode(implode("\n", $rows)),
            'filename' => 'cpid-classification.csv',
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CPID,
            'description' => 'CPID Classifications',
            'isExternal' => false,
            'isScan' => false,
            'user' => $item->getCreatedBy()->getId()
        ];

        try {
            $this->commandHandler->handleCommand(Upload::create($dtoData));

            return $this->success($item, 'Organisation list exported.');
        } catch (\Exception $ex) {
            return $this->failed($item, 'Unable to export list. '. $ex->getMessage());
        }
    }

    /**
     * Converts ['foo', '"bar"', 'cake,']
     * Into "foo","\"bar\"","cake,"
     *
     * @todo This may be useful elsewhere?
     *
     * @param $fields
     * @param string $separator
     * @param string $enclosure
     * @param string $escape
     * @return string
     */
    private function arrayToCsv($fields, $separator = ',', $enclosure = '"', $escape = '\\')
    {
        // Escape the enclosure character
        foreach ($fields as $key => $field) {
            $fields[$key] = str_replace($enclosure, $escape . $enclosure, $field);
        }

        // Enclose each field, and join on the separator
        return $enclosure . implode($enclosure . $separator . $enclosure, $fields) . $enclosure;
    }

    private function success(QueueEntity $item, $message = null)
    {
        $command = CompleteCmd::create(['item' => $item]);
        $this->commandHandler->handleCommand($command);

        return 'Successfully processed message: ' . $item->getId() . ' ' . $item->getOptions()
            . ($message ? ' ' . $message : '');
    }

    public function failed(QueueEntity $item, $reason = null)
    {
        $command = FailedCmd::create(['item' => $item]);
        $this->commandHandler->handleCommand($command);

        return 'Failed to process message: ' . $item->getId() . ' ' . $item->getOptions() . ' ' .  $reason;
    }
}
