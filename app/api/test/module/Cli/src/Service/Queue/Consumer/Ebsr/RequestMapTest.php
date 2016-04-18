<?php

/**
 * RequestMap Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Ebsr;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Ebsr\RequestMap as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Dvsa\Olcs\Api\Entity\User\User;

/**
 * RequestMap Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class RequestMapTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $user = new User('pid', 'type');
        $user->setId(1);
        $item = new QueueEntity();
        $item->setOptions(json_encode(['foo' => 'bar']));
        $item->setCreatedBy($user);

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['foo' => 'bar', 'user' => 1], $result);
    }
}
