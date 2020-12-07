<?php

namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Service\Document\Document;
use Dvsa\Olcs\DocumentShare\Data\object\File;
use Dvsa\Olcs\DocumentShare\Service\DocManClient;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObj;
use Laminas\I18n\Translator\TranslatorInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @covers Dvsa\Olcs\Api\Service\Document\Document
 */
class DocumentTest extends TestCase
{
    protected $docManClient;

    /** @var  Document */
    private $sut;

    public function setUp(): void
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

        /** @var \Laminas\ServiceManager\ServiceLocatorInterface|MockObj $serviceLocator */
        $serviceLocator = $this->createMock(\Laminas\ServiceManager\ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('DateService')
            ->willReturn($helperMock);

        $this->sut->setServiceLocator($serviceLocator);

        $replaced = $this->sut->populateBookmarks(
            $file,
            []
        );
        $this->assertEquals(
            $content,
            $replaced
        );
    }

    public function testPopulateBookmarksWithDynamicBookmarksImplementingFileStoreAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart TC_SIGNATURE} {\*\bkmkend TC_SIGNATURE}.";
        $this->setServiceManager();
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

    public function testPopulateBookmarksWithDynamicBookmarksImplementingTranslatorAwareInterface()
    {
        $content = "Bookmark 1: {\*\bkmkstart User_Access} {\*\bkmkend User_Access}.";

        $file = new File();
        $file->setContent($content);

        $translatorMock = $this->createMock(TranslatorInterface::class);

        /** @var \Laminas\ServiceManager\ServiceLocatorInterface|MockObj $serviceLocator */
        $serviceLocator = $this->createMock(ServiceLocatorInterface::class);
        $serviceLocator->expects($this->once())
            ->method('get')
            ->with('translator')
            ->willReturn($translatorMock);

        $this->sut->setServiceLocator($serviceLocator);

        $replaced = $this->sut->populateBookmarks(
            $file,
            []
        );
        $this->assertEquals(
            $content,
            $replaced
        );
    }

    private function setServiceManager(): void
    {
        $this->docManClient = m::mock(DocManClient::class);
        $sm = m::mock(\Laminas\ServiceManager\ServiceLocatorInterface::class)
            ->shouldReceive('get')->with('ContentStore')->andReturn(
                $this->docManClient
            )->getMock();
        $this->sut->setServiceLocator($sm);
    }
}
