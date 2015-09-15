<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Continuation;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Continuation as ContinuationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Create continuation
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Continuation';

    protected $extraRepos = ['Licence', 'ContinuationDetail'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $continuation = $this->getRepo()->fetchContinuation(
            $command->getMonth(),
            $command->getYear(),
            $command->getTrafficArea()
        );
        if (count($continuation)) {
            $result->addId('continuation', $continuation[0]->getId());
            $result->addMessage('Continuation exists');
            return $result;
        }

        $licences = $this->getRepo('Licence')->fetchForContinuation(
            $command->getYear(),
            $command->getMonth(),
            $command->getTrafficArea()
        );
        if (!count($licences)) {
            $result->addId('continuation', 0);
            $result->addMessage('No licences found');
            return $result;
        }
        $id = $this->createContinuation(
            $command->getYear(),
            $command->getMonth(),
            $command->getTrafficArea()
        );
        $this->createContinuationDetails(
            $id,
            $licences
        );

        $result->addId('continuation', $id);
        $result->addMessage('Continuation created');

        return $result;
    }

    protected function createContinuation($year, $month, $trafficArea)
    {
        $continuation = new ContinuationEntity();
        $continuation->setYear($year);
        $continuation->setMonth($month);
        $continuation->setTrafficArea($this->getRepo()->getReference(TrafficAreaEntity::class, $trafficArea));
        $this->getRepo()->save($continuation);
        return $continuation->getId();
    }

    protected function createContinuationDetails($id, $licences)
    {
        foreach ($licences as $licence) {
            $continuationDetail = new ContinuationDetailEntity();
            $continuationDetail->setLicence($licence);
            $continuationDetail->setReceived('N');
            $continuationDetail->setStatus(
                $this->getRepo()->getRefdataReference(ContinuationDetailEntity::STATUS_PREPARED)
            );
            $continuationDetail->setContinuation(
                $this->getRepo()->getReference(ContinuationEntity::class, $id)
            );
            $this->getRepo('ContinuationDetail')->save($continuationDetail);
        }
    }
}
