<?php

namespace Dvsa\OlcsTest\Api\Domain\Exception;

use Dvsa\Olcs\Api\Domain\Exception\DisabledHandlerException;

/**
 * Class DisabledHandlerExceptionTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class DisabledHandlerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testMessageContainsClassName()
    {
        $className = get_class(new \stdClass());
        $sut = new DisabledHandlerException($className);
        $this->assertContains($className, $sut->getMessage());
    }
}
