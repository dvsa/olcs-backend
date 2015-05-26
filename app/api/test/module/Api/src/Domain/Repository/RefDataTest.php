<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Repository\RefData;

/**
 * Class RefDataTest
 * @package OlcsTest\Db\Entity\Repository
 */
class RefDataTest extends \PHPUnit_Framework_TestCase
{
    public function testFindAllByCategoryAndLanguage()
    {
        $category = 'category';
        $lang = 'en_GB';
        $data = [['id' => 'category.1', 'description' => 'First Category']];

        $expectedParams = [
            [
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            ],
            [\Gedmo\Translatable\TranslatableListener::HINT_FALLBACK, 1],
            [\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $lang]
        ];
        $expectedCallsRemaining = 3;
        $matcher = function ($p1, $p2) use (&$expectedParams, &$expectedCallsRemaining) {
            $parameters = [$p1, $p2];

            $expectedCallsRemaining--;
            $matchKey = array_search($parameters, $expectedParams);

            $this->assertNotFalse(
                $matchKey,
                "Called parameters were not found in the expected values, or were used more than once"
            );
            unset($expectedParams[$matchKey]);
        };

        \Closure::bind($matcher, $this, $this);

        $mockQ = $this->getMock('\StdClass', ['setHint', 'getArrayResult']);
        $mockQ->expects($this->any())->method('setHint')->willReturnCallback($matcher);
        $mockQ->expects($this->once())->method('getArrayResult')->willReturn($data);

        $mockEm = $this->getMock('\Doctrine\ORM\EntityManagerInterface');

        $mockQb = $this->getMock('\Doctrine\ORM\QueryBuilder', ['getQuery'], [], '', false);
        $mockQb->expects($this->once())->method('getQuery')->willReturn($mockQ);

        $mockEm->expects($this->once())->method('createQueryBuilder')->willReturn($mockQb);

        $mockMetaData = $this->getMock(
            '\Doctrine\ORM\Mapping\ClassMetadata',
            [],
            ['Olcs\Db\Entity\Repository\RefData']
        );

        $sut = new RefData($mockEm, $mockMetaData);
        $result = $sut->findAllByCategoryAndLanguage($category, $lang);

        $this->assertEquals($data, $result);
        $this->assertEquals(0, $expectedCallsRemaining, 'setHint was not called the expected number of times');
    }
}
