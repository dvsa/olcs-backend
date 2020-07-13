<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\ContinuationReview\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Snapshot\Service\Snapshots\ContinuationReview\Section\PeopleReviewService;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use OlcsTest\Bootstrap;

/**
 * People review service test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PeopleReviewServiceTest extends MockeryTestCase
{
    /** @var PeopleReviewService review service */
    protected $sut;

    protected $mockTranslator;

    public function setUp(): void
    {
        $this->sut = new PeopleReviewService();

        $this->mockTranslator = m::mock(Translate::class)
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($arg) {
                    return $arg . '_translated';
                }
            )
            ->getMock();

        $this->sm = Bootstrap::getServiceManager();
        $this->sut->setServiceLocator($this->sm);
        $this->sm->setService('translator', $this->mockTranslator);
    }

    public function testGetConfigFromData()
    {
        $continuationDetail = new ContinuationDetail();

        $organisationPersons = new ArrayCollection();

        $organisationPerson1 = m::mock()
            ->shouldReceive('getPerson')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getTitle')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getDescription')
                            ->andReturn('Mr')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getForename')
                    ->andReturn('Foo')
                    ->once()
                    ->shouldReceive('getFamilyName')
                    ->andReturn('Bar')
                    ->once()
                    ->shouldReceive('getBirthDate')
                    ->with(true)
                    ->andReturn(new \DateTime('1980-02-01'))
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $organisationPerson2 = m::mock()
            ->shouldReceive('getPerson')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getTitle')
                    ->andReturn(
                        m::mock()
                            ->shouldReceive('getDescription')
                            ->andReturn('Doctor')
                            ->once()
                            ->getMock()
                    )
                    ->once()
                    ->shouldReceive('getForename')
                    ->andReturn('Baz')
                    ->once()
                    ->shouldReceive('getFamilyName')
                    ->andReturn('Cake')
                    ->once()
                    ->shouldReceive('getBirthDate')
                    ->with(true)
                    ->andReturn(null)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $organisationPersons->add($organisationPerson1);
        $organisationPersons->add($organisationPerson2);

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOrganisationPersons')
                    ->andReturn($organisationPersons)
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $expected =[
            [
                ['value' => 'continuations.people-section.table.name', 'header' => true],
                ['value' => 'continuations.people-section.table.date-of-birth', 'header' => true]
            ],
            [
                ['value' => 'Doctor Baz Cake'],
                ['value' => '']
            ],
            [
                ['value' => 'Mr Foo Bar'],
                ['value' => '01/02/1980']
            ]
        ];

        $this->assertEquals($expected, $this->sut->getConfigFromData($continuationDetail));
    }

    public function testGetConfigFromDataNoPersons()
    {
        $continuationDetail = new ContinuationDetail();

        $organisationPersons = new ArrayCollection();

        $mockLicence = m::mock(Licence::class)
            ->shouldReceive('getOrganisation')
            ->andReturn(
                m::mock()
                    ->shouldReceive('getOrganisationPersons')
                    ->andReturn($organisationPersons)
                    ->once()
                    ->shouldReceive('getType')
                    ->andReturn(
                        m::mock()
                        ->shouldReceive('getId')
                        ->andReturn('org_t_rc')
                        ->once()
                        ->getMock()
                    )
                    ->once()
                    ->getMock()
            )
            ->once()
            ->getMock();

        $continuationDetail->setLicence($mockLicence);

        $this->assertEquals(
            ['emptyTableMessage' => 'continuations.people-empty-table-message.org_t_rc_translated'],
            $this->sut->getConfigFromData($continuationDetail)
        );
    }
}
