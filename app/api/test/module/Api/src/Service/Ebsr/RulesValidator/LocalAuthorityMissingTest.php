<?php


namespace Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator;

use Dvsa\Olcs\Api\Service\Ebsr\RulesValidator\LocalAuthorityMissing;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LaEntity;

/**
 * Class LocalAuthorityMissingTest
 * @package Dvsa\OlcsTest\Api\Service\Ebsr\RulesValidator
 */
class LocalAuthorityMissingTest extends MockeryTestCase
{
    /**
     * tests whether missing local authorities not required are correctly identified
     *
     * @dataProvider isValidProvider
     *
     * @param ArrayCollection $la
     * @param ArrayCollection $naptan
     * @param $valid
     */
    public function testIsValid($la, $naptan, $valid)
    {
        $sut = new LocalAuthorityMissing();

        $value = [
            'localAuthoritys' => $la,
            'naptanAuthorities' => $naptan
        ];

        $this->assertEquals($valid, $sut->isValid($value));
    }

    /**
     * Provider for testIsValid
     *
     * @return array
     */
    public function isValidProvider()
    {
        $la1 = m::mock(LaEntity::class)->makePartial();
        $la1->setId(1);

        $la2 = m::mock(LaEntity::class)->makePartial();
        $la2->setId(2);

        $la3 = m::mock(LaEntity::class)->makePartial();
        $la3->setId(3);

        return [
            [new ArrayCollection([$la3, $la2]), new ArrayCollection([$la1, $la2]), false],
            [new ArrayCollection([$la3, $la2]), new ArrayCollection([$la3]), true],
            [new ArrayCollection([$la3]), new ArrayCollection([$la3]), true],
        ];
    }
}
