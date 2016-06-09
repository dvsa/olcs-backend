<?php

namespace Dvsa\OlcsTest\Api\Domain\Command\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\ProcessRequestMap;
use PHPUnit_Framework_TestCase;

/**
 * Process request map Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class ProcessRequestMapTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $id = 1;
        $user = 2;
        $scale = 'small';
        $template = 'template';
        $licence = 'licence';
        $regNo = '123/45678';

        $command = ProcessRequestMap::create(
            [
                'id' => $id,
                'user' => $user,
                'scale' => $scale,
                'template' => $template,
                'licence' => $licence,
                'regNo' => $regNo
            ]
        );

        $this->assertEquals($id, $command->getId());
        $this->assertEquals($user, $command->getUser());
        $this->assertEquals($scale, $command->getScale());
        $this->assertEquals($template, $command->getTemplate());
        $this->assertEquals($licence, $command->getLicence());
        $this->assertEquals($regNo, $command->getRegNo());
    }
}
