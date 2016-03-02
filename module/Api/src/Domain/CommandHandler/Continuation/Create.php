<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Continuation;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
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
        $continuations = $this->getRepo()->fetchContinuation(
            $command->getMonth(),
            $command->getYear(),
            $command->getTrafficArea()
        );
        if (count($continuations) === 0) {
            $continuation = $this->createContinuation(
                $command->getYear(),
                $command->getMonth(),
                $command->getTrafficArea()
            );
        } else {
            // assume the first one?
            $continuation = $continuations[0];
        }
        $this->result->addId('continuation', $continuation->getId());

        $licences = $this->getRepo('Licence')->fetchForContinuation(
            $command->getYear(),
            $command->getMonth(),
            $command->getTrafficArea()
        );
        if (!count($licences)) {
            $this->result->addId('continuation', 0);
            $this->result->addMessage('No licences found');
            return $this->result;
        }

        $this->createContinuationDetails($continuation, $licences);

        $this->result->addMessage('Continuation created');

        return $this->result;
    }

    /**
     * Create the continuation
     *
     * @param int    $year
     * @param int    $month
     * @param string $trafficArea
     *
     * @return ContinuationEntity
     */
    protected function createContinuation($year, $month, $trafficArea)
    {
        $continuation = new ContinuationEntity();
        $continuation->setYear($year);
        $continuation->setMonth($month);
        $continuation->setTrafficArea($this->getRepo()->getReference(TrafficAreaEntity::class, $trafficArea));
        $this->getRepo()->save($continuation);

        return $continuation;
    }

    /**
     * Create continuation details for licences
     *
     * @param ContinuationEntity $continuation
     * @param array              $licences
     */
    protected function createContinuationDetails(ContinuationEntity $continuation, $licences)
    {
        foreach ($licences as $licence) {
            // check if continuation details already exists for the licence
            $existingContinuationDetails = $this->getRepo('ContinuationDetail')->fetchForContinuationAndLicence(
                $continuation->getId(),
                $licence->getId()
            );
            // if not exists then create it
            if (count($existingContinuationDetails) === 0) {
                $continuationDetail = new ContinuationDetailEntity();
                $continuationDetail->setLicence($licence);
                $continuationDetail->setReceived('N');
                $continuationDetail->setStatus(
                    $this->getRepo()->getRefdataReference(ContinuationDetailEntity::STATUS_PREPARED)
                );
                $continuationDetail->setContinuation($continuation);

                $this->getRepo('ContinuationDetail')->save($continuationDetail);
            }
        }
    }
}
