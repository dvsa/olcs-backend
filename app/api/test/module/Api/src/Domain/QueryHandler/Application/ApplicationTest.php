<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\Application\Application;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Transfer\Query\Application\Application as Qry;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCmd;

/**
 * Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Application();

        $this->mockRepo('Application', ApplicationRepo::class);
        $this->mockRepo('Note', NoteRepo::class);
        $this->mockRepo('SystemParameter', \Dvsa\Olcs\Api\Domain\Repository\SystemParameter::class);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->shouldReceive('isAnonymous')->andReturn(false);

        $this->mockedSmServices = [
            'FeesHelperService' => m::mock(),
            'SectionAccessService' => m::mock(),
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')->andReturn(false)->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $applicationId = 111;

        $query = Qry::create(['id' => $applicationId, 'validateAppCompletion' => true]);

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application
            ->setId($applicationId)
            ->shouldReceive('getPublicationLinks')->with()->once()
                ->andReturn(new \Doctrine\Common\Collections\ArrayCollection())
            ->shouldReceive('getVariationCompletion')
            ->andReturn(null)
            ->once()
            ->shouldReceive('isPublishable')
            ->andReturn(true)
            ->once()
            ->shouldReceive('getLicence')
            ->andReturn(
                m::mock()
                ->shouldReceive('getId')
                ->andReturn(222)
                ->once()
                ->shouldReceive('getOrganisation')->andReturn(
                    m::mock(Organisation::class)
                        ->shouldReceive('isMlh')
                        ->once()
                        ->andReturn(true)
                        ->shouldReceive('getAllowedOperatorLocation')
                        ->once()
                        ->andReturn('GB')
                        ->getMock()
                )
                ->getMock()
            )
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->once()
            ->shouldReceive('serialize')->andReturn(['foo' => 'bar']);
        $application->setStatus((new \Dvsa\Olcs\Api\Entity\System\RefData())->setId('apsts_not_submitted'));
        $application->setIsVariation(true);

        $this->repoMap['SystemParameter']->shouldReceive('getDisableSelfServeCardPayments')->with()->once()
            ->andReturn(false);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with(222)
            ->andReturn('latest note');

        $sections = ['bar', 'cake'];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSections')
            ->once()
            ->with($application)
            ->andReturn($sections);

        $applicationFee = $this->getMockFee('100');
        $interimFee =  $this->getMockFee('66.70');
        $fees = [$applicationFee, $interimFee];
        $this->mockedSmServices['FeesHelperService']
            ->shouldReceive('getTotalOutstandingFeeAmountForApplication')
            ->with($applicationId)
            ->once()
            ->andReturn(166.70);

        $this->commandHandler
            ->shouldReceive('handleCommand')
            ->with(UpdateApplicationCompletionCmd::class)
            ->once()
            ->getMock();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake'],
            'outstandingFeeTotal' => '166.70',
            'variationCompletion' => null,
            'canCreateCase' => false,
            'existingPublication' => false,
            'isPublishable' => true,
            'latestNote' => 'latest note',
            'disableCardPayments' => false,
            'isMlh' => true,
            'allowedOperatorLocation' => 'GB',
            'canHaveInspectionRequest' => true,
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    protected function getMockFee($amount)
    {
        $mock = m::mock(FeeEntity::class)->makePartial();
        $mock
            ->setGrossAmount($amount)
            ->shouldReceive('getOutstandingAmount')
            ->andReturn($amount)
            ->shouldReceive('serialize')
            ->andReturn(['amount' => $amount]);
        return $mock;
    }
}
