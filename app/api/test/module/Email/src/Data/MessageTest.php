<?php

namespace Dvsa\OlcsTest\Email\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Message Test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class MessageTest extends MockeryTestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new Message('TO', 'SUBJECT');
    }

    public function testTo()
    {
        $this->assertSame('TO', $this->sut->getTo());

        $value = 'ABCDEFG';
        $this->sut->setTo($value);
        $this->assertSame($value, $this->sut->getTo());
    }

    public function testCc()
    {
        $value = ['ABCDEFG'];
        $this->sut->setCc($value);
        $this->assertSame($value, $this->sut->getCc());
    }

    public function testBcc()
    {
        $value = ['bcc1', 'bcc2'];
        $this->sut->setBcc($value);
        $this->assertSame($value, $this->sut->getBcc());
    }

    public function testDocs()
    {
        $value = ['doc1', 'doc2'];
        $this->sut->setDocs($value);
        $this->assertSame($value, $this->sut->getDocs());
    }

    public function testSubject()
    {
        $this->assertSame('SUBJECT', $this->sut->getSubject());

        $value = 'ABCDEFG';
        $this->sut->setSubject($value);
        $this->assertSame($value, $this->sut->getSubject());
    }

    public function testFromEmail()
    {
        $value = 'ABCDEFG';
        $this->sut->setFromEmail($value);
        $this->assertSame($value, $this->sut->getFromEmail());
    }

    public function testFromName()
    {
        $value = 'ABCDEFG';
        $this->sut->setFromName($value);
        $this->assertSame($value, $this->sut->getFromName());
    }

    public function testBody()
    {
        $value = 'ABCDEFG';
        $this->sut->setBody($value);
        $this->assertSame($value, $this->sut->getBody());
    }

    public function testHtml()
    {
        $value = 'ABCDEFG';
        $this->sut->setHtml($value);
        $this->assertSame($value, $this->sut->getHtml());
    }

    public function testLocale()
    {
        $value = 'ABCDEFG';
        $this->sut->setLocale($value);
        $this->assertSame($value, $this->sut->getLocale());
    }

    public function testSubjectVars()
    {
        $value = ['ABCDEFG', 'HIJ'];
        $this->sut->setSubjectVariables($value);
        $this->assertSame($value, $this->sut->getSubjectVariables());
    }

    public function testTranslateToWelshYes()
    {
        $this->assertSame('en_GB', $this->sut->getLocale());

        $this->sut->setTranslateToWelsh('Y');

        $this->assertSame('cy_GB', $this->sut->getLocale());
    }

    public function testTranslateToWelshNo()
    {
        $this->assertSame('en_GB', $this->sut->getLocale());

        $this->sut->setTranslateToWelsh('N');

        $this->assertSame('en_GB', $this->sut->getLocale());
    }

    public function testGetSubjectReplaceVars()
    {
        $this->sut->setSubject('My name is %s and I am %d years old');
        $this->sut->setSubjectVariables(['Billy', 82]);

        $this->assertSame('My name is Billy and I am 82 years old', $this->sut->getSubjectReplaceVariables());
    }
}
