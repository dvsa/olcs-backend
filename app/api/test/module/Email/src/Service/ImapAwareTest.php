<?php
namespace Olcs\Email\Service;

use Olcs\Email\Service\Imap as ImapService;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Class ImapAwareTest
 */
class ImapAwareTest extends TestCase
{
    public function testTrait()
    {
        $service = new ImapService();

        $trait = $this->getMockForTrait('Olcs\Email\Service\ImapAware');

        $this->assertSame($service, $trait->setImapService($service)->getImapService());
    }
}
