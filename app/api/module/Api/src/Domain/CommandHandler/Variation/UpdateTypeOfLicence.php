<?php

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee as CreateApplicationFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee as CancelFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\Fee\CancelFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;


/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTypeOfLicence extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $licence */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $licence = $application->getLicence();

        // If we are not trying to update the licence type
        if ($application->getLicenceType() === $this->getRepo()->getRefdataReference($command->getLicenceType())) {
            $result->addMessage('No updates required');
            return $result;
        }

        if (!$this->isGranted(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)) {
            throw new ForbiddenException('You do not have permission to update type of licence');
        }

        if (!$licence->canBecomeSpecialRestricted()
            && $command->getLicenceType() === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED
        ) {
            throw new ValidationException(
                [
                    'licenceType' => [
                        Licence::ERROR_CANT_BE_SR => 'You are not able to change licence type to special restricted'
                    ]
                ]
            );
        }

        $application->setLicenceType($this->getRepo()->getRefdataReference($command->getLicenceType()));

        $this->getRepo()->save($application);
        $result->addMessage('Application saved successfully');

        $result->merge($this->updateApplicationCompletion($command->getId()));

        $result->merge($this->handleVariationFees($application));

        return $result;
    }

    private function updateApplicationCompletion($id)
    {
        return $this->handleSideEffect(
            UpdateApplicationCompletion::create(['id' => $id, 'section' => 'typeOfLicence'])
        );
    }

    private function handleVariationFees(Application $application)
    {
        $result = new Result();

        foreach ($application->getFees() as $fee) {
            if ($fee->isVariationFee() && $fee->isFullyOutstanding()) {
                // only cancel and recreate variation fee if it is fully outstanding
                $result->merge($this->cancelAndRecreateVariationFee($fee, $application));
            }
        }

        return $result;
    }

    /**
     * Cancel existing fee and create a new one
     */
    private function cancelAndRecreateVariationFee(Fee $fee, Application $application)
    {
        return $this->handleSideEffects(
            [
                CancelFeeCmd::create(['id' => $fee->getId()]),
                CreateApplicationFeeCmd::create(
                    [
                        'id' => $application->getId(),
                        'feeTypeFeeType' => FeeType::FEE_TYPE_VAR
                    ]
                )
            ]
        );
    }
}
