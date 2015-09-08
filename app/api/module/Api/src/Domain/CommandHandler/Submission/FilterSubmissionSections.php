<?php

/**
 * Filter Submission
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Submission;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Submission\Submission;
use Dvsa\Olcs\Transfer\Command\Submission\FilterSubmissionSections as Cmd;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\SubmissionGeneratorAwareInterface;

/**
 * Filter Submission
 */
final class FilterSubmissionSections extends AbstractCommandHandler implements SubmissionGeneratorAwareInterface
{
    use SubmissionGeneratorAwareTrait;

    protected $repoServiceName = 'Submission';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Submission $submission */
        $submissionEntity = $this->filterSubmission($command);

        $this->getRepo()->save($submissionEntity);

        $result = new Result();
        $result->addId('submission', $submissionEntity->getId());
        $result->addMessage('Submission filtered successfully');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Submission
     */
    private function filterSubmission(Cmd $command)
    {
        /** @var Submission $submissionEntity */
        $submissionEntity = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // set section data prior to update in order to retain any other sections
        $dataSnapshot = json_decode($submissionEntity->getDataSnapshot(), true);

        foreach ($dataSnapshot as $sectionId => $sectionData) {
            if ($sectionId === $command->getSection()) {

                if (!empty($command->getSubSection()) &&
                    is_array($sectionData['data']['tables'][$command->getSubSection()])
                ) {
                    // filter a subsection table
                    $sectionData['data']['tables'][$command->getSubSection()] = $this->filterTable(
                        $sectionData['data']['tables'][$command->getSubSection()],
                        $command->getRowsToFilter()
                    );
                } else {
                    // filter section table
                    $sectionData['data']['tables'][$sectionId] = $this->filterTable(
                        $sectionData['data']['tables'][$sectionId],
                        $command->getRowsToFilter()
                    );
                }
            }

            $submissionEntity->setSectionData($sectionId, $sectionData);
        }

        $submissionEntity->setSubmissionDataSnapshot();

        return $submissionEntity;
    }

    private function filterTable($table, $rowsToRemove)
    {
        foreach ($table as $key => $dataRow) {
            if (in_array($dataRow['id'], $rowsToRemove)) {
                unset($table[$key]);
            }
        }
        ksort($table);

        return $table;
    }

}
