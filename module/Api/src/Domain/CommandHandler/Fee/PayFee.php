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
    protected $extraRepos = ['ContinuationDetail'];

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

        $continueLicenceResult = $this->maybeContinueLicence($fee);
        if ($continueLicenceResult !== false) {
            $result->merge($continueLicenceResult);
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

    /**
     * If all criteria are true then continue the Licence
     *
     * @param Fee $fee
     *
     * @return Result|false
     */
    protected function maybeContinueLicence(Fee $fee)
    {
        // Fee type is continuation fee
        if ($fee->getFeeType()->getFeeType()->getId() !== \Dvsa\Olcs\Api\Entity\Fee\FeeType::FEE_TYPE_CONT) {
            return false;
        }

        $licenceId = $fee->getLicence()->getId();

        // there is an ongoing continuation associated to a particular licence and the status is 'Checklist accepted'
        try {
            $this->getRepo('ContinuationDetail')->fetchOngoingForLicence($licenceId);
        } catch (\Doctrine\ORM\UnexpectedResultException $e) {
            return false;
        }

        // the licence status is Valid, Curtailed or Suspended
        $validLicenceStatuses = [
            \Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_VALID,
            \Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_CURTAILED,
            \Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_SUSPENDED,
        ];
        if (!in_array($fee->getLicence()->getStatus()->getId(), $validLicenceStatuses)) {
            return false;
        }

        // there are no other outstanding or (waive recommended) continuation fees associated to the licence
        $outstandingFees = $this->getRepo()->fetchOutstandingContinuationFeesByLicenceId($licenceId);
        if (count($outstandingFees) !== 0) {
            return false;
        }

        $result = $this->handleSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\ContinueLicence::create(['id' => $licenceId])
        );

        // add success message
        // @note not ideal to be using the FlashMessenger from a service, but in this circumstance it would be
        // difficult to get the return status all the way to the controller
        $result->addMessage('@todo Display message "licence.continued.message" to user');

        return $result;
    }
}
