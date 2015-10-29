<?php

/**
 * Refund Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareInterface;
use Dvsa\Olcs\Api\Domain\CpmsAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Refund Fee
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class RefundFee extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    CpmsAwareInterface
{
    use AuthAwareTrait, CpmsAwareTrait;

    protected $repoServiceName = 'Fee';

    public function handleCommand(CommandInterface $command)
    {
        /** @var FeeEntity $fee */
        $fee = $this->getRepo()->fetchUsingId($command);

        $response = $this->getCpmsService()->batchRefund($fee);

        // var_dump($response); exit;

        $this->result
            ->addMessage('Fee refunded');

        return $this->result;
    }
}
