<?php

namespace Dvsa\OlcsTest\Email\Data;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Email\Data\Message;
use Dvsa\Olcs\Email\Domain\Command\SendEmail as SendEmailCmd;

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

    public function testPlainBody()
    {
        $value = 'ABCDEFG';
        $this->sut->setPlainBody($value);
        $this->assertSame($value, $this->sut->getPlainBody());
    }

    public function testHtmlBody()
    {
        $value = 'ABCDEFG';
        $this->sut->setHtmlBody($value);
        $this->assertSame($value, $this->sut->getHtmlBody());
    }

    public function testHasHtml()
    {
        $value = 'ABCDEFG';
        $this->sut->setHasHtml($value);
        $this->assertSame($value, $this->sut->getHasHtml());
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

    public function testBuildCommand()
    {
        $this->sut->setFromName('from name');
        $this->sut->setFromEmail('from@test.me');
        $this->sut->setCc(['cc']);
        $this->sut->setBcc(['bcc1', 'bcc2']);
        $this->sut->setDocs(['doc1', 'doc2']);
        $this->sut->setSubject('subject');
        $this->sut->setSubjectVariables(['subVar']);
        $this->sut->setPlainBody('plain body');
        $this->sut->setHtmlBody('html body');

        $result = $this->sut->buildCommand();

        $this->assertInstanceOf(SendEmailCmd::class, $result);
        $this->assertEquals(
            [
                'fromName' => 'from name',
                'fromEmail' => 'from@test.me',
                'to' => 'TO',
                'cc' => ['cc'],
                'bcc' => ['bcc1', 'bcc2'],
                'subject' => 'subject',
                'subjectVariables' => ['subVar'],
                'docs' => ['doc1', 'doc2'],
                'plainBody' => 'plain body',
                'htmlBody' => 'html body',
                'locale' => 'en_GB'
            ],
            $result->getArrayCopy()
        );
    }
}
