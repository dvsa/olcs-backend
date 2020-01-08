<?php

namespace OlcsTest\Queue\Service\Message;

use Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\CompanyProfile;
use Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\ProcessInsolvency;
use Dvsa\Olcs\Queue\Service\Message\MessageBuilder;
use PHPUnit\Framework\TestCase;

class MessageBuilderTest extends TestCase
{
    protected $sut;

    public function setUp()
    {
        $this->sut = new MessageBuilder();
    }

    public function testBuildMessages()
    {
        $messages = $this->sut->buildMessages(
            [
                ['companyOrLlpNo' => 123],
                ['companyOrLlpNo' => 456]
            ],
            CompanyProfile::class,
            ['CompanyProfile_URL' => 'some_url']
        );

        $expected = [
            new CompanyProfile(['companyOrLlpNo' => 123], 'some_url'),
            new CompanyProfile(['companyOrLlpNo' => 456], 'some_url')
        ];

        $this->assertEquals($expected, $messages);
    }

    public function testBuildMessage()
    {
        $messages = $this->sut->buildMessage(
            ['companyOrLlpNo' => 123],
            ProcessInsolvency::class,
            ['ProcessInsolvency_URL' => 'some_url']
        );

        $expected = new ProcessInsolvency(['companyOrLlpNo' => 123], 'some_url');

        $this->assertEquals($expected, $messages);
    }

    public function testExceptionThrownIfInvalidMessageType()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The NonExistentClass class does not exist');

        $this->sut->buildMessages(
            [
                ['companyOrLlpNo' => 123],
            ],
            'NonExistentClass',
            ['NonExistentClass_URL' => 'some_url']
        );
    }

    public function testExceptionThrownIfQueueConfigDoesNotExist()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The url config CompanyProfile_URL does not exist');

        $this->sut->buildMessages(
            [
                ['companyOrLlpNo' => 123],
            ],
            CompanyProfile::class,
            ['Wrong_URL_key' => 'some_url']
        );
    }
}
