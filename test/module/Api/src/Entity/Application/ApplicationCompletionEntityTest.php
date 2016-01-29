<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Mockery as m;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion as Entity;

/**
 * ApplicationCompletion Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationCompletionEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testConstruct()
    {
        $application = m::mock(Application::class);

        $ac = new Entity($application);

        $this->assertSame($application, $ac->getApplication());
    }

    public function testIsCompleteEmpty()
    {
        $required = [];

        /** @var Entity $ac */
        $ac = $this->instantiate(Entity::class);

        $this->assertTrue($ac->isComplete($required));
    }

    public function testIsComplete()
    {
        $required = [
            'businessType'
        ];

        /** @var Entity $ac */
        $ac = $this->instantiate(Entity::class);
        $ac->setBusinessTypeStatus(Entity::STATUS_INCOMPLETE);

        $this->assertFalse($ac->isComplete($required));
    }

    public function testIsCompleteWhenComplete()
    {
        $required = [
            'businessType'
        ];

        /** @var Entity $ac */
        $ac = $this->instantiate(Entity::class);
        $ac->setBusinessTypeStatus(Entity::STATUS_COMPLETE);

        $this->assertTrue($ac->isComplete($required));
    }
}
