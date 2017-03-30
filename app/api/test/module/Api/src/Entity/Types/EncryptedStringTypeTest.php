<?php

namespace Dvsa\OlcsTest\Api\Entity\Types;

use PHPUnit_Framework_TestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Types\EncryptedStringType;

/**
 * Class EncryptedStringTypeTest
 * @covers \Dvsa\Olcs\Api\Entity\Types\EncryptedStringType
 */
class EncryptedStringTypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EncryptedStringType
     */
    private $sut;

    protected function setUp()
    {
        if (!EncryptedStringType::hasType(EncryptedStringType::TYPE)) {
            EncryptedStringType::addType(EncryptedStringType::TYPE, EncryptedStringType::class);
        }
        $this->sut = EncryptedStringType::getType(EncryptedStringType::TYPE);
    }

    public function testGetName()
    {
        $this->assertSame(EncryptedStringType::TYPE, $this->sut->getName());
    }

    public function testGetEncrypterNotSet()
    {
        $this->setExpectedException(\RuntimeException::class, 'An encrypter must be set to allow encrypting data');
        $this->sut->getEncrypter();
    }

    public function testSetGetEncrypter()
    {
        $blockCipher = m::mock('\Zend\Crypt\BlockCipher');
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame($blockCipher, $this->sut->getEncrypter());
    }

    public function testConvertToPhpValue()
    {
        $platform = $this->getMock('\Doctrine\DBAL\Platforms\MySqlPlatform');
        $blockCipher = m::mock('\Zend\Crypt\BlockCipher');
        $blockCipher->shouldReceive('decrypt')->with('ENCRYPTED')->once()->andReturn('DECRYPTED');
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame('DECRYPTED', $this->sut->convertToPHPValue('ENCRYPTED', $platform));
    }

    public function testConvertToDatabaseValue()
    {
        $platform = $this->getMock('\Doctrine\DBAL\Platforms\MySqlPlatform');
        $blockCipher = m::mock('\Zend\Crypt\BlockCipher');
        $blockCipher->shouldReceive('encrypt')->with('DECRYPTED')->once()->andReturn('ENCRYPTED');
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame('ENCRYPTED', $this->sut->convertToDatabaseValue('DECRYPTED', $platform));
    }
}
