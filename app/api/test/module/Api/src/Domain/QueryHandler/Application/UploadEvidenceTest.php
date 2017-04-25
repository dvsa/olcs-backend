<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Interim;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\UploadEvidence;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Transfer\Query\Application\UploadEvidence as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Entity\Category;
use Mockery as m;
use Zend\Feed\Reader\Collection;
use ZfcRbac\Service\AuthorizationService;

/**
 * UploadEvidenceTest
 */
class UploadEvidenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new UploadEvidence();
        $this->mockRepo('Application', ApplicationRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $documentCollection = new \Doctrine\Common\Collections\ArrayCollection(
            [
                new Document('doc1'),
                new Document('doc2'),
                new Document('doc3'),
            ]
        );

        /** @var ApplicationEntity|m\Mock $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->shouldReceive('getApplicationDocuments')
            ->with('CAT_REF', 'SUB_CAT_REF')->once()->andReturn($documentCollection);
        $application->shouldReceive('canAddFinancialEvidence')->with()->once()->andReturn('CAN_ADD_FE');

        $this->repoMap['Application']->shouldReceive('fetchById')->with(111)->once()->andReturn($application);
        $this->repoMap['Application']->shouldReceive('getCategoryReference')
            ->with(\Dvsa\Olcs\Api\Entity\System\Category::CATEGORY_APPLICATION)->once()->andReturn('CAT_REF');
        $this->repoMap['Application']->shouldReceive('getSubCategoryReference')
            ->with(SubCategory::DOC_SUB_CATEGORY_FINANCIAL_EVIDENCE_DIGITAL)->once()->andReturn('SUB_CAT_REF');

        $result = $this->sut->handleQuery($query);

        $this->assertArraySubset(
            [
                'financialEvidence' => [
                    'canAdd' => true,
                    'documents' => [
                        ['identifier' => 'doc1'],
                        ['identifier' => 'doc2'],
                        ['identifier' => 'doc3'],
                    ]
                ],
                'operatingCentres' => []
            ],
            $result
        );
    }
}
