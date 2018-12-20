<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Surrender\ByLicence as QryHandler;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Transfer\Query\Surrender\ByLicence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

class ByLicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QryHandler();
        $this->mockRepo('Surrender', Surrender::class);
        $this->mockRepo('SystemParameter', SystemParameter::class);
        parent::setUp();
    }

    public function testQueryHandle()
    {
        $query = ByLicence::create(['id' => 1]);
        $surrender = new Surrender();
        $surrender->setId(1);
        $this->repoMap['Surrender']->shouldReceive(
            'fetchOneByLicence'
        )->andReturn($surrender);
        $this->repoMap['SystemParameter']->shouldReceive(
            'getDisableGdsVerifySignatures'
        )->andReturn(true);
        $expected = new Result($surrender, ['licence','status','licenceDocumentStatus','communityLicenceDocumentStatus'], ['disableSignatures' => true]);
        $this->assertEquals($expected, $this->sut->handleQuery($query));
    }
}
