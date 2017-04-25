<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\CommandHandler\Application\UploadEvidence;
use Dvsa\Olcs\Api\Entity;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Application\UploadEvidence as Cmd;

/**
 * UploadEvidenceTest
 */
class UploadEvidenceTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UploadEvidence();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->categoryReferences = [
            Entity\System\Category::CATEGORY_APPLICATION => m::mock(Entity\System\Category::class),
        ];

        $this->subCategoryReferences = [
            Entity\System\SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL =>
                m::mock(Entity\System\SubCategory::class),
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $command = Cmd::create(['id' => 111]);

        $documentCollection = new \Doctrine\Common\Collections\ArrayCollection(
            [
                new Entity\Doc\Document('doc1'),
                new Entity\Doc\Document('doc2'),
            ]
        );

        /** @var Entity\Application\Application|m\Mock $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setFinancialEvidenceUploaded(Entity\Application\Application::FINANCIAL_EVIDENCE_SEND_IN_POST);
        $application->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[Entity\System\Category::CATEGORY_APPLICATION],
                $this->subCategoryReferences[Entity\System\SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )->once()->andReturn($documentCollection);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application)
            ->shouldReceive('save')->with($application)->once()->andReturn();

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Financial evidence uploaded',
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
        $this->assertSame(
            Entity\Application\Application::FINANCIAL_EVIDENCE_UPLOADED,
            $application->getFinancialEvidenceUploaded()
        );
    }

    public function testHandleCommandNoFinancialEvidenceDocs()
    {
        $command = Cmd::create(['id' => 111]);

        $documentCollection = new \Doctrine\Common\Collections\ArrayCollection(
            []
        );

        /** @var Entity\Application\Application|m\Mock $application */
        $application = m::mock(Entity\Application\Application::class)->makePartial();
        $application->setFinancialEvidenceUploaded(Entity\Application\Application::FINANCIAL_EVIDENCE_SEND_IN_POST);
        $application->shouldReceive('getApplicationDocuments')
            ->with(
                $this->categoryReferences[Entity\System\Category::CATEGORY_APPLICATION],
                $this->subCategoryReferences[Entity\System\SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL]
            )->once()->andReturn($documentCollection);

        $this->repoMap['Application']
            ->shouldReceive('fetchById')->with(111)->once()->andReturn($application);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
            ]
        ];
        $this->assertEquals($expected, $result->toArray());
        $this->assertSame(
            Entity\Application\Application::FINANCIAL_EVIDENCE_SEND_IN_POST,
            $application->getFinancialEvidenceUploaded()
        );
    }
}
