<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Conditions;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;

/**
 * Conditions test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class ConditionsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Conditions();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new Conditions();
        $bookmark->setData(
            [
                'conditionUndertakings' => [
                    [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntity::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntity::TYPE_CONDITION
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a note'
                    ], [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntity::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntity::TYPE_CONDITION
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a third note'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            "a note\n\na third note",
            $bookmark->render()
        );
    }
}
