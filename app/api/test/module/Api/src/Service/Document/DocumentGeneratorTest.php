<?php

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Service\Document\DocumentGenerator;

/**
 * Document Generation Helper Service test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class DocumentGeneratorTest extends MockeryTestCase
{
    public function testGenerateFromTemplateWithEmptyQuery()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn(
                m::mock()
                ->shouldReceive('read')
                ->with('/templates/x.rtf')
                ->andReturn('file')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn(
                m::mock()
                ->shouldReceive('getBookmarkQueries')
                ->with('file', [])
                ->andReturn([])
                ->shouldReceive('populateBookmarks')
                ->with('file', [])
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerator();
        $helper->setServiceLocator($sm);

        $helper->generateFromTemplate('x');
    }

    public function testGenerateFromTemplateWithQuery()
    {
        $query = [
            'a' => m::mock(QueryInterface::class),
            'b' => [
                m::mock(QueryInterface::class),
                m::mock(QueryInterface::class)
            ]
        ];

        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('ContentStore')
            ->andReturn(
                m::mock()
                ->shouldReceive('read')
                ->with('/templates/x.rtf')
                ->andReturn('file')
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn(
                m::mock()
                ->shouldReceive('handleQuery')
                ->once()
                ->with($query['a'])
                ->andReturn(['a' => 1])
                ->shouldReceive('handleQuery')
                ->once()
                ->with($query['b'][0])
                ->andReturn(['b' => 1])
                ->shouldReceive('handleQuery')
                ->once()
                ->with($query['b'][1])
                ->andReturn(['b' => 2])
                ->getMock()
            )
            ->shouldReceive('get')
            ->with('Document')
            ->andReturn(
                m::mock()
                ->shouldReceive('getBookmarkQueries')
                ->with('file', ['y' => 1])
                ->andReturn($query)
                ->shouldReceive('populateBookmarks')
                ->with('file', ['a' => ['a' => 1], 'b' => [['b' => 1], ['b' => 2]], 'z' => 2])
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerator();
        $helper->setServiceLocator($sm);

        $helper->generateFromTemplate('x', ['y' => 1], ['z' => 2]);
    }

    public function testUploadGeneratedContent()
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('FileUploader')
            ->andReturn(
                m::mock()
                ->shouldReceive('setFile')
                ->with(['content' => 'foo'])
                ->shouldReceive('upload')
                ->with('docs', 'bar')
                ->andReturn('result')
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerator();
        $helper->setServiceLocator($sm);

        $helper->uploadGeneratedContent('foo', 'docs', 'bar');
    }

    public function testGenerateAndStore()
    {
        /**
         * Mocking the SUT here is okay because this method is just a wrapper for
         * other public methods which are tested individually
         */
        $helper = m::mock(DocumentGenerator::class)
            ->makePartial();

        $helper->shouldReceive('addTemplatePrefix')
            ->with([], 'template')
            ->andReturn('prefix-template')
            ->shouldReceive('generateFromTemplate')
            ->with('prefix-template', [], [])
            ->andReturn('content')
            ->shouldReceive('uploadGeneratedContent')
            ->with('content', 'documents')
            ->andReturn('result');

        $this->assertEquals('result', $helper->generateAndStore('template'));
    }

    public function testAddTemplatePrefixWithNoMatchingKey()
    {
        $helper = new DocumentGenerator();

        $this->assertEquals('template', $helper->addTemplatePrefix([], 'template'));
    }

    /**
     * @dataProvider templatePrefixProvider
     */
    public function testAddTemplatePrefixWithMatchingKey($niFlag, $prefix)
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn(
                m::mock()
                ->shouldReceive('handleQuery')
                ->with(m::type(LicenceBundle::class))
                ->andReturn(
                    [
                        'niFlag' => $niFlag
                    ]
                )
                ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerator();
        $helper->setServiceLocator($sm);

        $this->assertEquals(
            $prefix . '/template',
            $helper->addTemplatePrefix(['licence' => 123], 'template')
        );
    }

    /**
     * @dataProvider templatePrefixProvider
     */
    public function testAddTemplatePrefixWithMatchingKeyApplication($niFlag, $prefix)
    {
        $sm = m::mock('Zend\ServiceManager\ServiceLocatorInterface')
            ->shouldReceive('get')
            ->with('QueryHandlerManager')
            ->andReturn(
                m::mock()
                    ->shouldReceive('handleQuery')
                    ->with(m::type(ApplicationBundle::class))
                    ->andReturn(
                        [
                            'niFlag' => $niFlag
                        ]
                    )
                    ->getMock()
            )
            ->getMock();

        $helper = new DocumentGenerator();
        $helper->setServiceLocator($sm);

        $this->assertEquals(
            $prefix . '/template',
            $helper->addTemplatePrefix(['application' => 123], 'template')
        );
    }

    public function templatePrefixProvider()
    {
        return [
            ['N', 'GB'],
            ['Y', 'NI']
        ];
    }
}
