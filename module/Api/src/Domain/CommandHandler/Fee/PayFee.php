<?php

/**
 * Pay Fee (handles fee side effects)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ValidateApplication;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Pay Fee (handles fee side effects)
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class PayFee extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Fee';

    /**
     * @see Common\Service\Listener\FeeListenerService
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $fee = $this->getRepo()->fetchUsingId($command);

        $applicationFeeResult = $this->maybeProcessApplicationFee($fee);

        if ($applicationFeeResult !== null) {
            $result->merge($applicationFeeResult);
        }

        return $result;
    }

    protected function maybeProcessApplicationFee(Fee $fee)
    {
        $application = $fee->getApplication();

        if ($application === null
            || $application->isVariation()
            || $application->getStatus()->getId() !== Application::APPLICATION_STATUS_GRANTED
        ) {
            return;
        }

        $outstandingGrantFees = $this->getRepo()->fetchOutstandingGrantFeesByApplicationId($application->getId());

        // if there are any outstanding GRANT fees then don't continue
        if (!empty($outstandingGrantFees)) {
            return;
        }

        return $this->handleSideEffect(
            ValidateApplication::create(['id' => $application->getId()])
        );
    }
}
