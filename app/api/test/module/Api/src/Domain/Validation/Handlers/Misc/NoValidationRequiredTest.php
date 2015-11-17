<?php

/**
 * No Validation Required Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\CompanySubsidiary\Application;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Misc\NoValidationRequired;
use Zend\ServiceManager\ServiceManager;

/**
 * No Validation Required Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NoValidationRequiredTest extends AbstractHandlerTestCase
{
    /**
     * @var NoValidationRequired
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new NoValidationRequired();

        parent::setUp();
    }

    public function testIsValid()
    {
        /** @var CommandInterface $dto */
        $dto = m::mock(CommandInterface::class);

        $this->assertTrue($this->sut->isValid($dto));
    }
}
