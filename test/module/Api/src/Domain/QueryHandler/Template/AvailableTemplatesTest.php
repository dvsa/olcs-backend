<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Email;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Template\AvailableTemplates;
use Dvsa\Olcs\Api\Domain\Repository\Template as TemplateRepo;
use Dvsa\Olcs\Api\Entity\Template\Template;
use Dvsa\Olcs\Transfer\Query\Template\AvailableTemplates as AvailableTemplatesQry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

class AvailableTemplatesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new AvailableTemplates();

        $this->mockRepo('Template', TemplateRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $template1Id = 3;
        $template1Locale = 'en_GB';
        $template1Format = 'plain';
        $template1Description = 'Email template for en_GB plain send ecmt successful';
        $template1CategoryName = 'Permits';
        $template1 = $this->createMockTemplate(
            $template1Id,
            $template1Locale,
            $template1Format,
            $template1Description,
            $template1CategoryName
        );

        $template2Id = 5;
        $template2Locale = 'cy_GB';
        $template2Format = 'html';
        $template2Description = 'Email template for cy_GB html send ecmt part successful';
        $template2CategoryName = 'Header/footer';
        $template2 = $this->createMockTemplate(
            $template2Id,
            $template2Locale,
            $template2Format,
            $template2Description,
            $template2CategoryName
        );
 
        $this->repoMap['Template']->shouldReceive('fetchAll')
            ->andReturn([$template1, $template2]);

        $expectedResponse = [
            [
                'id' => $template1Id,
                'locale' => $template1Locale,
                'format' => $template1Format,
                'description' => $template1Description,
                'category' => $template1CategoryName,
            ],
            [
                'id' => $template2Id,
                'locale' => $template2Locale,
                'format' => $template2Format,
                'description' => $template2Description,
                'category' => $template2CategoryName,
            ],
        ];

        $result = $this->sut->handleQuery(
            AvailableTemplatesQry::create([])
        );

        $this->assertEquals($expectedResponse, $result);
    }

    private function createMockTemplate($id, $locale, $format, $description, $computedCategoryName)
    {
        $template = m::mock(Template::class);
        $template->shouldReceive('getId')
            ->andReturn($id);
        $template->shouldReceive('getLocale')
            ->andReturn($locale);
        $template->shouldReceive('getFormat')
            ->andReturn($format);
        $template->shouldReceive('getDescription')
            ->andReturn($description);
        $template->shouldReceive('getComputedCategoryName')
            ->andReturn($computedCategoryName);

        return $template;
    }
}
