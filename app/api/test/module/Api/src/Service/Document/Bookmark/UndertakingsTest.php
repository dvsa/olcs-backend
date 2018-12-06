<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Undertakings;
use Dvsa\Olcs\Api\Entity\Cases\ConditionUndertaking as ConditionUndertakingEntity;

/**
 * Undertakings test
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class UndertakingsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new Undertakings();
        $query = $bookmark->getQuery(['licence' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $bookmark = new Undertakings();
        $bookmark->setData(
            [
                'conditionUndertakings' => [
                    [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntity::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
                        ],
                        'isFulfilled' => 'N',
                        'isDraft' => 'N',
                        'notes' => 'a note'
                    ], [
                        'attachedTo' => [
                            'id' => ConditionUndertakingEntity::ATTACHED_TO_LICENCE
                        ],
                        'conditionType' => [
                            'id' => ConditionUndertakingEntity::TYPE_UNDERTAKING
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
