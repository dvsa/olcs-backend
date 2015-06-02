<?php

/**
 * CancelFee
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Fee\Fee;

/**
 * CancelFee
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class CancelFee extends AbstractCommandHandler
{
    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $fee Fee */
        $fee = $this->getRepo()->fetchUsingId($command);
        $fee->setFeeStatus($this->getRepo()->getRefdataReference(Fee::STATUS_CANCELLED));
        $this->getRepo()->save($fee);

        $result = new Result();
        $result->addMessage('Fee '. $fee->getId() .' cancelled successfully');

        return $result;
    }
}
