<?php

/**
 * CreateTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Publication;

use Dvsa\Olcs\Api\Domain\Command\Publication\Create;
use PHPUnit_Framework_TestCase;

/**
 * CreateTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CreateTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $trafficArea = 'M';
        $pubStatus = 'pubStatus';
        $pubDate = '2015-12-25';
        $pubType = 'pub type';
        $publicationNo = 'publicationNo';
        $docTemplate = 'docTemplate';

        $command = Create::create(
            [
                'trafficArea' => $trafficArea,
                'pubStatus' => $pubStatus,
                'pubDate' => $pubDate,
                'pubType' => $pubType,
                'publicationNo' => $publicationNo,
                'docTemplate' => $docTemplate,
            ]
        );

        $this->assertEquals($trafficArea, $command->getTrafficArea());
        $this->assertEquals($pubStatus, $command->getPubStatus());
        $this->assertEquals($pubDate, $command->getPubDate());
        $this->assertEquals($pubType, $command->getPubType());
        $this->assertEquals($publicationNo, $command->getPublicationNo());
        $this->assertEquals($docTemplate, $command->getDocTemplate());
    }
}
