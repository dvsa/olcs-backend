<?php

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Command\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication;
use PHPUnit_Framework_TestCase;

/**
 * Update Application Completion Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ResetApplicationTest extends PHPUnit_Framework_TestCase
{
    public function testStructure()
    {
        $command = ResetApplication::create(
            [
                'id' => 111,
                'foo' => 'bar',
                'operatorType' => 'op-type',
                'licenceType' => 'lic-type',
                'niFlag' => 'Y',
                'confirm' => true
            ]
        );

        $this->assertEquals(111, $command->getId());
        $this->assertEquals('op-type', $command->getOperatorType());
        $this->assertEquals('lic-type', $command->getLicenceType());
        $this->assertEquals('Y', $command->getNiFlag());
        $this->assertEquals(true, $command->getConfirm());

        $this->assertEquals(
            [
                'id' => 111,
                'operatorType' => 'op-type',
                'licenceType' => 'lic-type',
                'niFlag' => 'Y',
                'confirm' => true
            ],
            $command->getArrayCopy()
        );
    }
}
