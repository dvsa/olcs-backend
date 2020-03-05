<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\IntJourneys;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\IntJourneysFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\EcmtShortTerm\IntJourneysGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\Radio;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\RadioGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * IntJourneysGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class IntJourneysGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTrueFalse
     */
    public function testGenerate($isNi)
    {
        $irhpApplication = m::mock(IrhpApplicationEntity::class);
        $irhpApplication->shouldReceive('getLicence->isNi')
            ->andReturn($isNi);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getQaEntity')
            ->andReturn($irhpApplication);

        $radio = m::mock(Radio::class);

        $radioGenerator = m::mock(RadioGenerator::class);
        $radioGenerator->shouldReceive('generate')
            ->with($elementGeneratorContext)
            ->once()
            ->andReturn($radio);

        $intJourneys = m::mock(IntJourneys::class);

        $intJourneysFactory = m::mock(IntJourneysFactory::class);
        $intJourneysFactory->shouldReceive('create')
            ->with($isNi, $radio)
            ->once()
            ->andReturn($intJourneys);

        $intJourneysGenerator = new IntJourneysGenerator($intJourneysFactory, $radioGenerator);

        $this->assertSame(
            $intJourneys,
            $intJourneysGenerator->generate($elementGeneratorContext)
        );
    }

    public function dpTrueFalse()
    {
        return [
            [true],
            [false]
        ];
    }
}
