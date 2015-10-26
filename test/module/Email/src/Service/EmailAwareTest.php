<?php
namespace Olcs\Email\Service;

use Olcs\Email\Service\Email as EmailService;
use PHPUnit_Framework_TestCase as TestCase;

class EmailAwareTest extends TestCase
{
    public function testTrait()
    {
        $service = new EmailService();

        $trait = $this->getMockForTrait('Olcs\Email\Service\EmailAware');

        $this->assertSame($service, $trait->setEmailService($service)->getEmailService());
    }
}