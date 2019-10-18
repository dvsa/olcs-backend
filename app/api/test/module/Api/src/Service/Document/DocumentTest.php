<?php

namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Service\Document\Document;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Dvsa\Olcs\DocumentShare\Service\DocManClient;
use Dvsa\Olcs\DocumentShare\Service\DocumentClientStrategy;
use Dvsa\Olcs\DocumentShare\Service\WebDavClient;
use Mockery as m;
use PHPUnit_Framework_MockObject_MockObject as MockObj;

/**
 * @covers Dvsa\Olcs\Api\Service\Document\Document
 */
class DocumentTest extends \PHPUnit\Framework\TestCase
{
    protected $docManClient;

    /** @var  Document */
    private $sut;

    public function setUp()
    {


        $this->sut = new Document();


    }

    public function testGetBookmarkQueriesForNoBookmarks()
    {
        $this->setServiceManager();
        $file = new File();
        $file->setContent('');

        $queryData = $this->sut->getBookmarkQueries($file, []);
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForStaticBookmarks()
    {


        $content = <<<TXT
Bookmark 1: {\*\bkmkstart letter_date_add_14_days} {\*\bkmkend letter_date_add_14_days}.
Boomkark 2: {\*\bkmkstart todays_date}{\*\bkmkend todays_date}
TXT;
        $file = new File();
        $file->setContent($content);

        $queryData = $this->sut->getBookmarkQueries($file, []);
        $this->assertEquals([], $queryData);
    }

    public function testGetBookmarkQueriesForDynamicConcreteBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart caseworker_name} {\*\bkmkend caseworker_name}
Bookmark 2: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}
TXT;
        $file = new File();
        $file->setContent($content);

        $queryData = $this->sut->getBookmarkQueries(
            $file,
            [
                'user' => 1,
                'licence' => 123
            ]
        );

        $this->assertArrayHasKey('caseworker_name', $queryData);
        $this->assertArrayHasKey('licence_number', $queryData);
    }

    public function testGetBookmarkQueriesForDynamicTextBlockBookmarks()
    {
        $content = <<<TXT
Bookmark 1: {\*\bkmkstart para_one} {\*\bkmkend para_one}
Bookmark 2: {\*\bkmkstart para_two} {\*\bkmkend para_two}
Bookmark 3: {\*\bkmkstart para_three} {\*\bkmkend para_three}
TXT;
        $file = new File();
        $file->setContent($content);

        $queryData = $this->sut->getBookmarkQueries(
            $file,
            [
                'bookmarks' => [
                    'para_one' => [1],
                    'para_three' => [2]
                ]
            ]
        );

        $this->assertArrayHasKey('para_one', $queryData);
        $this->assertArrayHasKey('para_three', $queryData);

        // we didn't supply any bookmark data for para two so we'd
        // expect it to not be in the query
        $this->assertArrayNotHasKey('para_two', $queryData);
    }

    public function testPopulateBookmarksWithStaticBookmarks()
    {
        $content = "Bookmark 1: {\*\bkmkstart todays_date} {\*\bkmkend todays_date}.";

        $file = new File();
        $file->setContent($content);

        $replaced = $this->sut->populateBookmarks(
            $file,
            []
        );

        // @NOTE: ideally we'd mock a todays_date bookmark instead of
        // using a real (and especially a date sensitive) one...
        $date = date("d/m/Y");

        $this->assertEquals(
            "Bookmark 1: " . $date . ".",
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarks()
    {
        $content = "Bookmark 1: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}.";

        $file = new File();
        $file->setContent($content);

        $replaced = $this->sut->populateBookmarks(
            $file,
            [
                'licence_number' => [
                    'licNo' => 1234
                ]
            ]
        );

        $this->assertEquals(
            "Bookmark 1: 1234.",
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksButNoData()
    {
        $content = "Bookmark 1: {\*\bkmkstart licence_number} {\*\bkmkend licence_number}.";

        $file = new File();
        $file->setContent($content);

        $replaced = $this->sut->populateBookmarks(
            $file,
            []
        );

        $this->assertEquals(
            $content,
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksImplementingDateAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart Serial_Num} {\*\bkmkend Serial_Num}.";

        $file = new File();
        $file->setContent($content);

        $helperMock = $this->createMock(\Dvsa\Olcs\Api\Service\Date::class);

        /** @var \Zend\ServiceManager\ServiceLocatorInterface|MockObj $serviceLocator */
        $serviceLocator = $this->createMock(\Zend\ServiceManager\ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('DateService')
            ->willReturn($helperMock);

        $this->sut->setServiceLocator($serviceLocator);

        $this->sut->populateBookmarks(
            $file,
            []
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksImplementingFileStoreAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart TC_SIGNATURE} {\*\bkmkend TC_SIGNATURE}.";
        $this->setServiceManager();
        $file = new File();
        $file->setContent($content);

        $this->sut->populateBookmarks(
            $file,
            []
        );
    }

    private function setServiceManager(): void
    {
        $this->docManClient = m::mock(DocManClient::class);
        $sm = m::mock(\Zend\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with(DocumentClientStrategy::class)->andReturn(
                m::mock(DocumentClientStrategy::class)->shouldReceive('getClientClass')->andReturn(
                    DocManClient::class
                )->getMock()
            )
            ->shouldReceive('get')->with(DocManClient::class)->andReturn($this->docManClient)
            ->getMock();
        $this->sut->setServiceLocator($sm);
    }
}
