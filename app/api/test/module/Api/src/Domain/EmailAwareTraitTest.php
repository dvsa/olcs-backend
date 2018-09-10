<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\EmailAwareTraitTestStub;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\User\User;
use Mockery as m;

/**
 * Email aware trait test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EmailAwareTraitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test organisation recipients when user is present
     */
    public function testOrganisationRecipients()
    {
        $userEmail = 'user@test.com';
        $orgEmails = ['orgEmail1@test.com'];

        $user = m::mock(User::class);
        $user->shouldReceive('getContactDetails->getEmailAddress')->once()->withNoArgs()->andReturn($userEmail);

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getAdminEmailAddresses')->once()->withNoArgs()->andReturn($orgEmails);

        $expected = [
            'to' => $userEmail,
            'cc' => $orgEmails,
        ];

        $sut = new EmailAwareTraitTestStub();
        $this->assertEquals($expected, $sut->organisationRecipients($organisation, $user));
    }

    /**
     * Test organisation recipients when there is no user
     */
    public function testOrganisationRecipientsNoUser()
    {
        $orgEmail1 = 'orgEmail1@test.com';
        $orgEmail2 = 'orgEmail2@test.com';

        $orgEmails = [
            $orgEmail1,
            $orgEmail2
        ];

        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getAdminEmailAddresses')->once()->withNoArgs()->andReturn($orgEmails);

        $expected = [
            'to' => $orgEmail1,
            'cc' => [1 => $orgEmail2],
        ];

        $sut = new EmailAwareTraitTestStub();
        $this->assertEquals($expected, $sut->organisationRecipients($organisation, null));
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\MissingEmailException
     */
    public function testOrganisationRecipientsException()
    {
        $organisation = m::mock(Organisation::class);
        $organisation->shouldReceive('getAdminEmailAddresses')->once()->withNoArgs()->andReturn([]);

        $sut = new EmailAwareTraitTestStub();
        $sut->organisationRecipients($organisation, null);
    }
}
