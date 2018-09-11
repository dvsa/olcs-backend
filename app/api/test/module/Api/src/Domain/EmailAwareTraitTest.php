<?php

namespace Dvsa\OlcsTest\Api\Domain;

use Dvsa\Olcs\Api\Domain\CommandHandler\EmailAwareTraitTestStub;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Mockery as m;

/**
 * Email aware trait test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class EmailAwareTraitTest extends m\Adapter\Phpunit\MockeryTestCase
{
    /**
     * Test organisation recipients when user and contact details present
     */
    public function testOrganisationRecipients()
    {
        $userEmail = 'user@test.com';
        $orgEmails = ['orgEmail1@test.com'];

        $contactDetails = m::mock(ContactDetails::class);
        $contactDetails->shouldReceive('getEmailAddress')->once()->withNoArgs()->andReturn($userEmail);

        $user = m::mock(User::class);
        $user->shouldReceive('getContactDetails')->once()->withNoArgs()->andReturn($contactDetails);

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
     * Test organisation recipients when there is no user, or when the user has no contact details
     *
     * @dataProvider emptyUserProvider
     */
    public function testOrganisationRecipientsNoUserDetails($user)
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
        $this->assertEquals($expected, $sut->organisationRecipients($organisation, $user));
    }

    public function emptyUserProvider()
    {
        $user = m::mock(User::class);
        $user->shouldReceive('getContactDetails')->withNoArgs()->andReturnNull();

        return [
            [null],
            [$user]
        ];
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
