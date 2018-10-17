<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;


/**
 * Upload the log output for the permit scoring
 * batch process
 *
 */
final class UploadScoringResult extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpCandidatePermit';

    /**
    * Handle command
    *
    * @param CommandInterface $command command
    *
    * @return Result
    *
    * @todo: row of headers is currently the array keys, which aren't reader-friendly. Change this.
    */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $csvContent = $command->getCsvContent();

        //  create csv file in memory
        $fh = fopen("php://temp", 'w');

        if (!empty(($csvContent))) {
            fputcsv($fh, array_keys($csvContent[0])); //row of headers
        }

        foreach ($csvContent as $dataRow) {
            fputcsv($fh, current($csvContent));
            next($csvContent);
        }

        rewind($fh);
        $content = stream_get_contents($fh);

        fclose($fh);

        $data = [
            'content' => base64_encode($content),
            'category' => Category::CATEGORY_PERMITS,
            'subCategory' => SubCategory::REPORT_SUB_CATEGORY_PERMITS,
            'filename' => 'international-goods-list.csv',
            'description' => 'Scoring Result File ' . date('d/m/Y'),
            'user' => \Dvsa\Olcs\Api\Rbac\PidIdentityProvider::SYSTEM_USER,
        ];

        unset($content);

        $document = $this->handleSideEffect(
            UploadCmd::create($data)
        );

        $result->addMessage('Scoring results file successfully uploaded.');

        return $result;
    }
}
