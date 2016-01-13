<?php

/**
 * CreateNextPublicationTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Publication;

use Dvsa\Olcs\Api\Domain\Command\Publication\CreateNextPublication;
use PHPUnit_Framework_TestCase;

/**
 * CreateNextPublicationTest
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class CreateNextPublicationTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $id = 5;
        $command = CreateNextPublication::create(['id' => $id]);
        $this->assertEquals($id, $command->getId());
    }
}
