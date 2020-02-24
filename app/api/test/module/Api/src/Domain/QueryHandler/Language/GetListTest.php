<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Language;

use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Language\GetList as QueryHandler;
use Dvsa\Olcs\Api\Entity\System\Language as LanguageEntity;
use Dvsa\Olcs\Transfer\Query\Language\GetList as ListQuery;

/**
 * GetList Test
 */
class GetListTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $query = ListQuery::create([]);
        $result = $this->sut->handleQuery($query);

        $expected = [
            'languages' => LanguageEntity::SUPPORTED_LANGUAGES
        ];

        $this->assertEquals($expected, $result);
    }
}
