<?php

/**
 * UpdateTrafficArea
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;

/**
 * UpdateTrafficArea
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateTrafficArea extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $licence->setTrafficArea($this->getRepo()->getReference(TrafficArea::class, $command->getTrafficArea()));

        $this->getRepo()->save($licence);

        $result->addMessage('Licence Traffic Area updated');
        return $result;
    }
}
