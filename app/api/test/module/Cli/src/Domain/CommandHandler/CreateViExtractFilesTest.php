<?php

/**
 * Create VI Extract Files Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Cli\Domain\CommandHandler\CreateViExtractFiles;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Create VI Extract Files Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateViExtractFilesTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateViExtractFiles();
        $this->mockRepo('ViOcView', Repository\ViOcView::class);
        $this->mockRepo('ViOpView', Repository\ViOpView::class);
        $this->mockRepo('ViTnmView', Repository\ViTnmView::class);
        $this->mockRepo('ViVhlView', Repository\ViVhlView::class);

        $this->mockedSmServices['Config'] = [
            'batch_config' => [
                'vi-extract-files' => [
                    'export-path' => '/tmp'
                ]
            ]
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $params = [
            'op' => true,
            'oc' => true,
            'tnm' => true,
            'vhl' => true,
            'all' => null,
            'path' => '/tmp'
        ];

        $this->repoMap['ViOpView']->shouldReceive('fetchForExport')
            ->once()->andReturn(['line' => 'foo']);

        $this->repoMap['ViOcView']->shouldReceive('fetchForExport')
            ->once()->andReturn(['line' => 'bar']);

        $this->repoMap['ViTnmView']->shouldReceive('fetchForExport')
            ->once()->andReturn(['line' => 'cake']);

        $this->repoMap['ViVhlView']->shouldReceive('fetchForExport')
            ->once()->andReturn([]);

        $response = $this->sut->handleCommand(\Dvsa\Olcs\Cli\Domain\Command\CreateViExtractFiles::create($params));

        $expected = [
            'id' => [],
            'messages' => [
                'Found 1 record(s) for Operating Centres',
                '1 record(s) saved for Operating Centres',
                'Found 1 record(s) for Operators',
                '1 record(s) saved for Operators',
                'Found 1 record(s) for Trading Names',
                '1 record(s) saved for Trading Names',
                'Found 0 record(s) for Vehicles',
            ]
        ];
        $this->assertEquals($expected, $response->toArray());
    }
}
