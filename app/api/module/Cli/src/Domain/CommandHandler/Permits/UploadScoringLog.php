<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\Upload as UploadCmd;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;

/**
 * Upload the log output for the permit scoring
 * batch process
 *
 */
final class UploadScoringLog extends ScoringCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';

    /**
    * Handle command
    *
    * @param CommandInterface $command command
    *
    * @return Result
    */
    public function handleCommand(CommandInterface $command)
    {
        $this->profileMessage('upload scoring log...');

        $result = new Result();

        $content = $command->getLogContent();

        $data = [
            'content' => base64_encode($content),
            'category' => Category::CATEGORY_PERMITS,
            'subCategory' => SubCategory::REPORT_SUB_CATEGORY_PERMITS,
            'filename' => 'Permit-Scoring-Log.log',
            'description' => 'Scoring Log File ' . date('Y-m-d H:i'),
            'user' => PidIdentityProvider::SYSTEM_USER,
        ];

        $this->handleSideEffect(
            UploadCmd::create($data)
        );

        $result->addMessage('Log file successfully uploaded.');

        return $result;
    }
}
