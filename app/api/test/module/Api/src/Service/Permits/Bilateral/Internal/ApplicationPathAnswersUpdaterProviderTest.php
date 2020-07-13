<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationPathAnswersUpdaterInterface;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationPathAnswersUpdaterProvider;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * ApplicationPathAnswersUpdaterProviderTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationPathAnswersUpdaterProviderTest extends MockeryTestCase
{
    const APPLICATION_PATH_GROUP_ID_1 = 56;
    const APPLICATION_PATH_GROUP_ID_2 = 62;

    private $updater1;

    private $updater2;

    private $applicationPathAnswersUpdaterProvider;

    public function setUp(): void
    {
        $this->updater1 = m::mock(ApplicationPathAnswersUpdaterInterface::class);
        $this->updater2 = m::mock(ApplicationPathAnswersUpdaterInterface::class);

        $this->applicationPathAnswersUpdaterProvider = new ApplicationPathAnswersUpdaterProvider();

        $this->applicationPathAnswersUpdaterProvider->registerUpdater(
            self::APPLICATION_PATH_GROUP_ID_1,
            $this->updater1
        );

        $this->applicationPathAnswersUpdaterProvider->registerUpdater(
            self::APPLICATION_PATH_GROUP_ID_2,
            $this->updater2
        );
    }

    public function testGetByApplicationPathGroupIdUpdater1()
    {
        $this->assertSame(
            $this->updater1,
            $this->applicationPathAnswersUpdaterProvider->getByApplicationPathGroupId(self::APPLICATION_PATH_GROUP_ID_1)
        );
    }

    public function testGetByApplicationPathGroupIdUpdater2()
    {
        $this->assertSame(
            $this->updater2,
            $this->applicationPathAnswersUpdaterProvider->getByApplicationPathGroupId(self::APPLICATION_PATH_GROUP_ID_2)
        );
    }

    public function testGetByApplicationPathGroupIdException()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to find updater corresponding to application path group id 77');

        $this->assertSame(
            $this->updater2,
            $this->applicationPathAnswersUpdaterProvider->getByApplicationPathGroupId(77)
        );
    }
}
