<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Cli\Domain\Command\Permits\UploadScoringResult as UploadScoringResultCommand;
use Dvsa\Olcs\Cli\Domain\CommandHandler\Permits\UploadScoringResult as UploadScoringResultHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Command\Document\Upload;
use Dvsa\Olcs\Api\Rbac\PidIdentityProvider;
use Mockery as m;

/**
 * Upload scoring result test
 *
 * @author Jason de Jonge <jason.de-jonge@capgemini.co.uk>
 */
class UploadScoringResultTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new UploadScoringResultHandler();

        parent::setUp();
    }

    /**
     * tests handleCommand
     */
    public function testHandleCommand()
    {
        $csvData = [
            0 => [
                'test',
            ]
        ];
        $fileDesc = 'TEST';
        $csvContent = 'MAp0ZXN0Cg=='; //this is the output of base64_encode($csvData)
        $result1 = new Result();
        $result1->addMessage('Document created');

        $this->expectedSideEffect(
            Upload::class,
            [
              'content' => $csvContent,
              'category' => Category::CATEGORY_PERMITS,
              'subCategory' => SubCategory::REPORT_SUB_CATEGORY_PERMITS,
              'filename' => 'Permit-Scoring-Report.csv',
              'description' => $fileDesc . ' ' . date('Y-m-d H:i'),
              'user' => PidIdentityProvider::SYSTEM_USER,
            ],
            $result1
        );

        $this->sut->handleCommand(
            UploadScoringResultCommand::create(['csvContent' => $csvData, 'fileDescription' => $fileDesc])
        );
    }
}
