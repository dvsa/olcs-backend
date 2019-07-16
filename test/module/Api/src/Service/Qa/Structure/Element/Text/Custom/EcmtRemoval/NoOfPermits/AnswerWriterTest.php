<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepository;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as IrhpPermitWindowRepository;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits\AnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits\IrhpPermitApplicationFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * AnswerWriterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnswerWriterTest extends MockeryTestCase
{
    private $permitsRequired;

    private $irhpApplicationEntity;

    private $irhpPermitApplicationEntity;

    private $irhpPermitApplicationFactory;

    private $irhpPermitApplicationRepo;

    private $irhpPermitWindowRepo;

    private $currentDateTimeFactory;

    public function setUp()
    {
        $this->permitsRequired = 345;

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->irhpPermitApplicationEntity = m::mock(IrhpPermitApplicationEntity::class);
        $this->irhpPermitApplicationEntity->shouldReceive('updatePermitsRequired')
            ->with($this->permitsRequired)
            ->once();

        $this->irhpPermitApplicationFactory = m::mock(IrhpPermitApplicationFactory::class);

        $this->irhpPermitApplicationRepo = m::mock(IrhpPermitApplicationRepository::class);
        $this->irhpPermitApplicationRepo->shouldReceive('save')
            ->with($this->irhpPermitApplicationEntity)
            ->once();

        $this->irhpPermitWindowRepo = m::mock(IrhpPermitWindowRepository::class);

        $this->currentDateTimeFactory = m::mock(CurrentDateTimeFactory::class);

        $this->answerWriter = new AnswerWriter(
            $this->irhpPermitApplicationFactory,
            $this->irhpPermitApplicationRepo,
            $this->irhpPermitWindowRepo,
            $this->currentDateTimeFactory
        );
    }

    public function testWriteExistingIrhpPermitApplication()
    {
        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitApplications->first')
            ->andReturn($this->irhpPermitApplicationEntity);

        $this->answerWriter->write($this->irhpApplicationEntity, $this->permitsRequired);
    }

    public function testWriteNewIrhpPermitApplication()
    {
        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitApplications->first')
            ->andReturn(null);

        $currentDateTime = m::mock(DateTime::class);
        $this->currentDateTimeFactory->shouldReceive('create')
            ->andReturn($currentDateTime);

        $irhpPermitWindowEntity = m::mock(IrhpPermitWindowEntity::class);
        $this->irhpPermitWindowRepo->shouldReceive('fetchLastOpenWindowByIrhpPermitType')
            ->with(IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL, $currentDateTime)
            ->andReturn($irhpPermitWindowEntity);

        $this->irhpPermitApplicationFactory->shouldReceive('create')
            ->with($this->irhpApplicationEntity, $irhpPermitWindowEntity)
            ->once()
            ->andReturn($this->irhpPermitApplicationEntity);

        $this->answerWriter->write($this->irhpApplicationEntity, $this->permitsRequired);
    }
}
