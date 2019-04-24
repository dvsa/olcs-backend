<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Email;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Template\TemplateSource;
use Dvsa\Olcs\Api\Domain\Repository\Template as TemplateRepo;
use Dvsa\Olcs\Transfer\Query\Template\TemplateSource as TemplateSourceQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class TemplateSourceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new TemplateSource();

        $this->mockRepo('Template', TemplateRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $templateId = 45;
        $source = '{{var1}} test {{var2}}';
        $locale = 'en_GB';
        $format = 'plain';

        $template = m::mock(Template::class);
        $template->shouldReceive('getSource')
            ->andReturn($source);
        $template->shouldReceive('getLocale')
            ->andReturn($locale);
        $template->shouldReceive('getFormat')
            ->andReturn($format);

        $query = TemplateSourceQry::create(
            ['id' => $templateId]
        );

        $this->repoMap['Template']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($template);

        $expectedResponse = [
            'source' => $source,
            'locale' => $locale,
            'format' => $format
        ];

        $result = $this->sut->handleQuery($query);
        $this->assertEquals($expectedResponse, $result);
    }
}
