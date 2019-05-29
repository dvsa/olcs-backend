<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationValidation;
use Dvsa\Olcs\Api\Service\Qa\Element\ValidatorFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\Validator;
use Dvsa\Olcs\Api\Service\Qa\Element\ValidatorGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * ValidatorGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ValidatorGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $rule = 'Between';

        $parametersJson = '{"min": 1, "max": 2}';
        $decodedParameters = [
            'min' => 1,
            'max' => 2
        ];

        $applicationValidation = m::mock(ApplicationValidation::class);
        $applicationValidation->shouldReceive('getRule')
            ->andReturn($rule);
        $applicationValidation->shouldReceive('getParameters')
            ->andReturn($parametersJson);

        $validator = m::mock(Validator::class);

        $validatorFactory = m::mock(ValidatorFactory::class);
        $validatorFactory->shouldReceive('create')
            ->with($rule, $decodedParameters)
            ->once()
            ->andReturn($validator);

        $validatorGenerator = new ValidatorGenerator($validatorFactory);

        $this->assertSame(
            $validator,
            $validatorGenerator->generate($applicationValidation)
        );
    }
}
