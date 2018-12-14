<?php

namespace Dvsa\OlcsTest\Api\Entity\Types;

use phpseclib\Crypt\AES;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Types\EncryptedStringType;

/**
 * Class EncryptedStringTypeTest
 * @covers \Dvsa\Olcs\Api\Entity\Types\EncryptedStringType
 */
class EncryptedStringTypeTest extends \PHPUnit\Framework\TestCase
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
        $this->expectException(\RuntimeException::class, 'An encrypter must be set to allow encrypting data');
        $this->sut->getEncrypter();
    }

    public function testSetGetEncrypter()
    {
        $blockCipher = m::mock(AES::class);
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame($blockCipher, $this->sut->getEncrypter());
    }

    public function testConvertToPhpValue()
    {
        $platform = $this->createMock('\Doctrine\DBAL\Platforms\MySqlPlatform');
        $blockCipher = m::mock(AES::class);
        $blockCipher->shouldReceive('decrypt')->with('ENCRYPTED')->once()->andReturn('DECRYPTED');
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame('DECRYPTED', $this->sut->convertToPHPValue(base64_encode('ENCRYPTED'), $platform));
    }

    public function testConvertToDatabaseValue()
    {
        $platform = $this->createMock('\Doctrine\DBAL\Platforms\MySqlPlatform');
        $blockCipher = m::mock(AES::class);
        $blockCipher->shouldReceive('encrypt')->with('DECRYPTED')->once()->andReturn('ENCRYPTED');
        $this->sut->setEncrypter($blockCipher);
        $this->assertSame(base64_encode('ENCRYPTED'), $this->sut->convertToDatabaseValue('DECRYPTED', $platform));
    }
}
