<?php

/**
 * SubmissionGenerator Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Submission\SubmissionGenerator;
use Dvsa\Olcs\Api\Entity\Submission\Submission as SubmissionEntity;
use Dvsa\Olcs\Api\Entity\System\RefData as RefdataEntity;

use Dvsa\Olcs\Api\Entity\Cases\Cases as CaseEntity;

/**
 * SubmissionGenerator Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class SubmissionGeneratorTest extends MockeryTestCase
{
    protected $sut;

    public function testConstructor()
    {
        $mockConfig = 'foo';
        $mockSectionGeneratorPluginManager = m::mock();
        $sut = new SubmissionGenerator($mockConfig, $mockSectionGeneratorPluginManager);

        $this->assertInstanceOf(SubmissionGenerator::class, $sut);
    }

    /**
     * Test exception thrown for invalid exception types
     * @throws \Exception
     */
    public function testGenerateSubmissionInvalidSubmissionType()
    {
        $mockConfig = 'foo';
        $mockSectionGeneratorPluginManager = m::mock();
        $mockSubmission = m::mock(SubmissionEntity::class)->makePartial();
        $mockCase = m::mock(CaseEntity::class)->makePartial();
        $mockCase->setId(77);
        $mockSubmission->setSubmissionType(new RefdataEntity('invalid_submission_type'));

        $sections = [];

        $sut = new SubmissionGenerator($mockConfig, $mockSectionGeneratorPluginManager);

        $mockSubmission->shouldReceive('getCase')->andReturn($mockCase);

        $mockCase->shouldReceive('isTm')->andReturn(false);

        $this->setExpectedException('Exception', 'Invalid submission type');

        $sut->generateSubmission($mockSubmission, $sections);
    }

    /**
     * Test valid submission is generated
     *
     * @param $submissionType
     * @param $expectedSections
     * @throws \Exception
     */
    public function testGenerateSubmissionValidSubmission()
    {
        $sectionId = 'valid_submission_type';
        $mockConfig = [
            'section-types' => [
                'valid_submission_type' => [
                    'section1',
                    'section2'
                ]
            ]
        ];
        $mockSectionGeneratorPluginManager = m::mock();

        /** @var SubmissionEntity $mockSubmission */
        $mockSubmission = m::mock(SubmissionEntity::class)->makePartial();
        $mockCase = m::mock(CaseEntity::class)->makePartial();
        $mockCase->setId(77);
        $mockSubmission->setSubmissionType(new RefdataEntity($sectionId));

        $sections = [];

        $sut = new SubmissionGenerator($mockConfig, $mockSectionGeneratorPluginManager);

        $mockSubmission->shouldReceive('getCase')->andReturn($mockCase);

        $mockCase->shouldReceive('isTm')->andReturn(false);

        $mockSectionGeneratorPluginManager->shouldReceive('get')
            ->with(m::type('string'))
            ->times(count($mockConfig['section-types'][$sectionId]))
            ->andReturnSelf()
            ->shouldReceive('generateSection')
            ->with($mockCase)
            ->andReturn('foo');

        $result = $sut->generateSubmission($mockSubmission, $sections);
        $this->assertEquals('{"section1":"foo","section2":"foo"}', $mockSubmission->getDataSnapshot());
        $this->assertArrayHasKey('section1', $mockSubmission->getSectionData());
        $this->assertEquals(count($mockConfig['section-types'][$sectionId]), count($mockSubmission->getSectionData()));
        $this->assertArrayHasKey('section2', $mockSubmission->getSectionData());
        $this->assertEquals('foo', $mockSubmission->getSectionData()['section1']);
        $this->assertSame($result, $mockSubmission);
    }

    /**
     * Branch test for TM submissions that remove sections
     *
     * @param $submissionType
     * @param $expectedSections
     * @throws \Exception
     */
    public function testGenerateSubmissionValidTmSubmission()
    {
        $sectionId = 'valid_submission_type';
        $mockConfig = [
            'excluded-tm-sections' => ['case-summary', 'outstanding-applications', 'people'],
            'section-types' => [
                'valid_submission_type' => [
                    'section1',
                    'section2',
                    'case-summary',
                    'outstanding-applications',
                    'people',
                    'section6'
                ]
            ]
        ];
        $mockSectionGeneratorPluginManager = m::mock();

        /** @var SubmissionEntity $mockSubmission */
        $mockSubmission = m::mock(SubmissionEntity::class)->makePartial();
        $mockCase = m::mock(CaseEntity::class)->makePartial();
        $mockCase->setId(77);
        $mockSubmission->setSubmissionType(new RefdataEntity($sectionId));

        $sections = [];

        $sut = new SubmissionGenerator($mockConfig, $mockSectionGeneratorPluginManager);

        $mockSubmission->shouldReceive('getCase')->andReturn($mockCase);

        $mockCase->shouldReceive('isTm')->andReturn(true);

        $mockSectionGeneratorPluginManager->shouldReceive('get')
            ->with(m::type('string'))
            ->andReturnSelf()
            ->shouldReceive('generateSection')
            ->with($mockCase)
            ->andReturn('foo');

        $result = $sut->generateSubmission($mockSubmission, $sections);
        $this->assertEquals('{"section1":"foo","section2":"foo","section6":"foo"}', $mockSubmission->getDataSnapshot());
        $this->assertArrayHasKey('section1', $mockSubmission->getSectionData());
        $this->assertArrayHasKey('section2', $mockSubmission->getSectionData());
        $this->assertEquals('foo', $mockSubmission->getSectionData()['section1']);
        $this->assertSame($result, $mockSubmission);

        $this->assertNotContains('case-outline', $mockSubmission->getSectionData());
        $this->assertNotContains('outstanding-applications', $mockSubmission->getSectionData());
        $this->assertNotContains('people', $mockSubmission->getSectionData());
    }
}
