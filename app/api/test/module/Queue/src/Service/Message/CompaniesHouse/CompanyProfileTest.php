<?php

namespace OlcsTest\Queue\Service\Message\CompaniesHouse;

use Dvsa\Olcs\Queue\Service\Message\CompaniesHouse\CompanyProfile;
use PHPUnit\Framework\TestCase;

class CompanyProfileTest extends TestCase
{
    protected $sut;


    public function testProcessMessageData()
    {
        $this->sut = new CompanyProfile(
            ['companyOrLlpNo' => 632],
            'some_url'
        );

        $expected = [
            'QueueUrl' => 'some_url',
            'MessageBody' => 632,
            'DelaySeconds' => 1
        ];

        $this->assertEquals($expected, $this->sut->toArray());
    }

    public function testProcessMessageException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("companyOrLlpNo is required in messageData");

        new CompanyProfile(
            [],
            'some_url'
        );
    }
}
