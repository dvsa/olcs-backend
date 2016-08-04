<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer\Tm;

use Dvsa\Olcs\Api\Entity\Queue\Queue as QueueEntity;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\Tm\UpdateTmNysiisName as Sut;
use Dvsa\OlcsTest\Cli\Service\Queue\Consumer\AbstractConsumerTestCase;
use Zend\Serializer\Adapter\Json as ZendJson;
use Zend\ServiceManager\Exception\ServiceNotCreatedException as ZendServiceException;

/**
 * Update Tm Nysiis name Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class UpdateTmNysiisNameTest extends AbstractConsumerTestCase
{
    protected $consumerClass = Sut::class;

    public function testGetCommandData()
    {
        $item = new QueueEntity();
        $item->setEntityId(111);
        $item->setOptions(json_encode(['foo' => 'bar']));

        $result = $this->sut->getCommandData($item);

        $this->assertEquals(['id' => 111, 'foo' => 'bar'], $result);
    }
}
