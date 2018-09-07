<?php

namespace Dvsa\OlcsTest\Api\Entity\Permits;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;



/**
 * IrhpCandidatePermit Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class IrhpCandidatePermitEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testCreateNew()
    {
         $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
         $IrhpPermitRange = m::mock(IrhpPermitRange::class);
         $intensityOfUse = 2.1;
         $randomizedScore = 1.0;
         $applicationScore = 2.11;

         $permit = Entity::createNew(
            $irhpPermitApplication,
            $IrhpPermitRange,
            $intensityOfUse,
            $randomizedScore,
            $applicationScore
         );

         $this->assertEquals($irhpPermitApplication, $permit->getIrhpPermitApplication());
         $this->assertEquals($IrhpPermitRange, $permit->getIrhpPermitRange());
         $this->assertEquals($intensityOfUse, $permit->getIntensityOfUse());
         $this->assertEquals($randomizedScore, $permit->getRandomizedScore());
         $this->assertEquals($applicationScore, $permit->getApplicationScore());
    }
}
