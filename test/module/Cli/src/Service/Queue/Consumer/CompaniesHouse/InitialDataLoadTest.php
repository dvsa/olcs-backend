<?php

/**
 * Companies House Initial Data Load Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CompaniesHouse\InitialDataLoad as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;

/**
 * Companies House Initial Data Load Queue Consumer Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class InitialDataLoadTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testProcessMessageSuccess()
    {
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"companyNumber":"01234567"}');

        $expectedDtoData = ['companyNumber' => '01234567'];
        $cmdResult = new Result();
        $cmdResult
            ->addId('companiesHouseCompany', 101)
            ->addMessage('Company added');

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\InitialLoad::class,
            $expectedDtoData,
            $cmdResult
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Complete::class,
            ['item' => $item],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Successfully processed message: 99 {"companyNumber":"01234567"} Company added',
            $result
        );
    }

    public function testProcessMessageFailure()
    {
        $item = new QueueEntity();
        $item->setId(99);
        $item->setOptions('{"companyNumber":"01234567"}');

        $this->expectCommandException(
            \Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\InitialLoad::class,
            ['companyNumber' => '01234567'],
            \Dvsa\Olcs\Api\Domain\Exception\Exception::class,
            'epic fail'
        );

        $this->expectCommand(
            \Dvsa\Olcs\Api\Domain\Command\Queue\Failed::class,
            ['item' => $item],
            new Result(),
            false
        );

        $result = $this->sut->processMessage($item);

        $this->assertEquals(
            'Failed to process message: 99 {"companyNumber":"01234567"} epic fail',
            $result
        );
    }
}
