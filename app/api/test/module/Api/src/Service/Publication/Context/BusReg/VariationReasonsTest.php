<?php

namespace Dvsa\OlcsTest\Api\Service\Publication\Context\Bus;

use Dvsa\Olcs\Api\Service\Publication\Context\BusReg\VariationReasons;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Domain\QueryHandler\QueryHandlerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Class VariationReasonsTest
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class VariationReasonsTest extends MockeryTestCase
{

    /**
     * Tests bus reg variation reasons filter
     *
     * @group publicationFilter
     * @dataProvider testProvideDataProvider
     *
     * @param ArrayCollection $variationReasons
     * @param string $expectedString
     */
    public function testProvide(ArrayCollection $variationReasons, $expectedString)
    {
        $busReg = m::mock(BusRegEntity::class);
        $busReg->shouldReceive('getVariationReasons')->andReturn($variationReasons);

        $publication = m::mock(PublicationLink::class);
        $publication->shouldReceive('getBusReg')->andReturn($busReg);

        $sut = new VariationReasons(m::mock(QueryHandlerInterface::class));

        $output = [
            'variationReasons' => $expectedString
        ];

        $expectedOutput = new \ArrayObject($output);

        $this->assertEquals($expectedOutput, $sut->provide($publication, new \ArrayObject()));
    }

    public function testProvideDataProvider()
    {
        $reason1 = 'reason 1';
        $reason2 = 'reason 2';
        $reason3 = 'reason 3';

        $variationReason1 = new RefData();
        $variationReason1->setDescription($reason1);

        $variationReason2 = new RefData();
        $variationReason2->setDescription($reason2);

        $variationReason3 = new RefData();
        $variationReason3->setDescription($reason3);

        $noVariationReason = new ArrayCollection();
        $singleVariationReason = new ArrayCollection([$variationReason1]);
        $threeVariationReasons = new ArrayCollection([$variationReason1, $variationReason2, $variationReason3]);

        return [
            [$noVariationReason, null],
            [$singleVariationReason, 'reason 1'],
            [$threeVariationReasons, 'reason 1, reason 2 and reason 3']
        ];
    }
}
