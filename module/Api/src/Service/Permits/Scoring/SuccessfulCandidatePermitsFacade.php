<?php

namespace Dvsa\Olcs\Api\Service\Permits\Scoring;

use Dvsa\Olcs\Api\Domain\Command\Result;

class SuccessfulCandidatePermitsFacade
{
    /** @var SuccessfulCandidatePermitsGenerator */
    private $successfulCandidatePermitsGenerator;

    /** @var SuccessfulCandidatePermitsWriter */
    private $successfulCandidatePermitsWriter;

    /** @var SuccessfulCandidatePermitsLogger */
    private $successfulCandidatePermitsLogger;

    /**
     * Create service instance
     *
     * @param SuccessfulCandidatePermitsGenerator $successfulCandidatePermitsGenerator
     * @param SuccessfulCandidatePermitsWriter $successfulCandidatePermitsWriter
     * @param SuccessfulCandidatePermitsLogger $successfulCandidatePermitsLogger
     *
     * @return SuccessfulCandidatePermitsFacade
     */
    public function __construct(
        SuccessfulCandidatePermitsGenerator $successfulCandidatePermitsGenerator,
        SuccessfulCandidatePermitsWriter $successfulCandidatePermitsWriter,
        SuccessfulCandidatePermitsLogger $successfulCandidatePermitsLogger
    ) {
        $this->successfulCandidatePermitsGenerator = $successfulCandidatePermitsGenerator;
        $this->successfulCandidatePermitsWriter = $successfulCandidatePermitsWriter;
        $this->successfulCandidatePermitsLogger = $successfulCandidatePermitsLogger;
    }

    /**
     * Get an array containing successful candidate permit ids and the emissions categories assigned to them
     *
     * @param array $candidatePermits
     * @param int $stockId
     * @param int $quotaRemaining
     *
     * @return array
     */
    public function generate(array $candidatePermits, $stockId, $quotaRemaining)
    {
        return $this->successfulCandidatePermitsGenerator->generate($candidatePermits, $stockId, $quotaRemaining);
    }

    /**
     * Marks a series of candidate permits as successful and records the emissions category assigned to each
     *
     * @param array $candidatePermits
     */
    public function write(array $candidatePermits)
    {
        $this->successfulCandidatePermitsWriter->write($candidatePermits);
    }

    /**
     * Logs a list of successful candidate permits to a CQRS result object
     *
     * @param array $candidatePermits
     * @param Result $result
     */
    public function log(array $candidatePermits, Result $result)
    {
        $this->successfulCandidatePermitsLogger->log($candidatePermits, $result);
    }
}
