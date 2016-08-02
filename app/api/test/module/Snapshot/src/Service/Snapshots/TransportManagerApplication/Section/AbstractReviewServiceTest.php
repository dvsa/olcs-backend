<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\AbstractReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\AbstractReviewService
 */
class AbstractReviewServiceTest extends MockeryTestCase
{
    /** @var  AbstractReviewServiceStub */
    private $sut;

    public function setUp()
    {
        $this->sut = new AbstractReviewServiceStub();
    }

    /**
     * @dataProvider dpTestFormatFileNameOnly
     */
    public function testFormatFileNameOnly($path, $expect)
    {
        static::assertEquals($expect, $this->sut->formatFileNameOnly($path));
    }

    public function dpTestFormatFileNameOnly()
    {
        return [
            [
                'path' => '\\\\SERVER\\DOCS\\fileName1.DOC',
                'expect' => 'fileName1.DOC',
            ],
            [
                'path' => 'c:/docuemtns/aaa bbb/dir1/dir2/logn file name with spaces_and.ext',
                'expect' => 'logn file name with spaces_and.ext',
            ],
            [
                'path' => 'simple-File_name.Ext',
                'expect' => 'simple-File_name.Ext',
            ],
            [
                'path' => null,
                'expect' => '',
            ],
        ];
    }
}

/**
 * Stub class for testing AbstractReviewService
 */
class AbstractReviewServiceStub extends AbstractReviewService
{
    public function formatFileNameOnly($filePath)
    {
        return parent::formatFileNameOnly($filePath);
    }

    public function getConfig(TransportManagerApplication $tma)
    {
    }
}
