<?php

/**
 * Case Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
namespace OlcsTest\EntityTrait;

/**
 * Case Trait Test
 *
 * @author Craig Reasbeck <craig.reasbeck@valtech.co.uk>
 */
class CaseTraitTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Set Get Case
     */
    public function testSetGetCase()
    {
        $case = new \Olcs\Db\Entity\VosaCase();

        $this->assertSame($case, $this->getNewSut()->setCase($case)->getCase());
    }

    /**
     * Gets an instance of the sut.
     *
     * @return \Olcs\Db\EntityTrait\CaseTrait
     */
    public function getNewSut()
    {
        return $this->getMockForTrait('\Olcs\Db\EntityTrait\CaseTrait');
    }
}
