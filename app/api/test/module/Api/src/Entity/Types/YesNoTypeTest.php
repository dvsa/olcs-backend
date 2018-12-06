<?php

/**
 * Test YesNoType
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Entity\Types;

use PHPUnit_Framework_TestCase;
use Dvsa\Olcs\Api\Entity\Types\YesNoType;

/**
 * Test YesNoType
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class YesNoTypeTest extends PHPUnit_Framework_TestCase
{
    private $type;

    protected function setUp()
    {
        if (!YesNoType::hasType('yesno')) {
            YesNoType::addType('yesno', YesNoType::class);
        }
        $this->type = YesNoType::getType('yesno');
    }

    /**
     * test getSqlDeclaration
     */
    public function testGetSqlDeclaration()
    {
        $mockPlatform = $this->createMock('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $this->assertEquals(
            'tinyint(1) NOT NULL COMMENT \'(DC2Type:yesno)\'',
            $this->type->getSqlDeclaration(array(), $mockPlatform)
        );
    }

    /**
     * test convertToPHPValue
     *
     * @dataProvider providerConvertToPHPValue
     */
    public function testConvertToPhpValue($input, $output)
    {
        $mockPlatform = $this->createMock('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $this->assertEquals($output, $this->type->convertToPHPValue($input, $mockPlatform));
    }

    /**
     * Provider for convertToPHPValue
     */
    public function providerConvertToPhpValue()
    {
        return array(
            array(true, 'Y'),
            array(false, 'N'),
            array(1, 'Y'),
            array(0, 'N')
        );
    }

    /**
     * test convertToDatabaseValue
     *
     * @dataProvider providerConvertToDatabaseValue
     */
    public function testConvertToDatabaseValue($input, $output)
    {
        $mockPlatform = $this->createMock('\Doctrine\DBAL\Platforms\MySqlPlatform');

        $this->assertEquals($output, $this->type->convertToDatabaseValue($input, $mockPlatform));
    }

    /**
     * Provider for convertToDatabaseValue
     */
    public function providerConvertToDatabaseValue()
    {
        return array(
            array('y', 1),
            array('Y', 1),
            array('Yes', 1),
            array('YES', 1),
            array('yes', 1),
            array('n', 0),
            array('N', 0),
            array('No', 0),
            array('NO', 0),
            array('no', 0)
        );
    }

    /**
     * test getName
     */
    public function testGetName()
    {
        $this->assertEquals('yesno', $this->type->getName());
    }
}
