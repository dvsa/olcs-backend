<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Text\Custom;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\TotAuthVehiclesGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Text;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\TextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\ValidatorList;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TotAuthVehiclesGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TotAuthVehiclesGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $totAuthVehicles = 14;

        $validator = m::mock(Validator::class);
        $validator->shouldReceive('setParameter')
            ->with('min', 1)
            ->once();
        $validator->shouldReceive('setParameter')
            ->with('max', $totAuthVehicles)
            ->once();

        $validatorList = m::mock(ValidatorList::class);
        $validatorList->shouldReceive('getValidatorByRule')
            ->with('Between')
            ->andReturn($validator);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getIrhpApplicationEntity->getLicence->getTotAuthVehicles')
            ->andReturn($totAuthVehicles);
        $elementGeneratorContext->shouldReceive('getValidatorList')
            ->andReturn($validatorList);

        $hintTranslateableText = m::mock(TranslateableText::class);
        $hintTranslateableText->shouldReceive('setParameter')
            ->with(0, $totAuthVehicles)
            ->once();

        $text = m::mock(Text::class);
        $text->shouldReceive('getHint')
            ->andReturn($hintTranslateableText);

        $textGenerator = m::mock(TextGenerator::class);
        $textGenerator->shouldReceive('generate')
            ->with($elementGeneratorContext)
            ->andReturn($text);

        $totAuthVehiclesGenerator = new TotAuthVehiclesGenerator($textGenerator);

        $this->assertSame(
            $text,
            $totAuthVehiclesGenerator->generate($elementGeneratorContext)
        );
    }
}