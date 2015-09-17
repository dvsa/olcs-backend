<?php

/**
 * Create Variation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Application\ApplicationTracking;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVariation as Cmd;

/**
 * Create Variation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateVariation extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['Application'];

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        $shouldUpdateApplicationCompletion = false;

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        if ($licence->canHaveVariation() === false) {
            throw new ForbiddenException('Unable to create variation due to the licence status.');
        }

        if ($this->isGranted(Permission::INTERNAL_USER)) {
            $status = $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_UNDER_CONSIDERATION);
        } else {
            $status = $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_NOT_SUBMITTED);
        }

        $variation = new Application($licence, $status, true);
        $variation->copyInformationFromLicence($licence);

        $applicationCompletion = new ApplicationCompletion($variation);
        $variation->setApplicationCompletion($applicationCompletion);

        $applicationTracking = new ApplicationTracking($variation);
        $variation->setApplicationTracking($applicationTracking);

        if ($command->getLicenceType() !== null) {
            $variation->setLicenceType($this->getRepo()->getRefdataReference($command->getLicenceType()));
            $shouldUpdateApplicationCompletion = true;
        }

        if ($this->isGranted(Permission::INTERNAL_USER) && $command->getReceivedDate() !== null) {
            $variation->setReceivedDate(new DateTime($command->getReceivedDate()));
            $variation->setTargetCompletionDateFromReceivedDate();
        }

        if ($this->isGranted(Permission::SELFSERVE_USER)) {
            $variation->setAppliedVia($this->getRepo()->getRefdataReference(Application::APPLIED_VIA_SELFSERVE));
        }
        if ($this->isGranted(Permission::INTERNAL_USER)) {
            $variation->setAppliedVia($this->getRepo()->getRefdataReference($command->getAppliedVia()));
        }

        $this->getRepo('Application')->save($variation);

        $result->addId('application', $variation->getId());
        $result->addMessage('Variation created');

        if ($shouldUpdateApplicationCompletion) {
            $data = ['id' => $variation->getId(), 'section' => 'typeOfLicence'];
            $result->merge($this->handleSideEffect(UpdateApplicationCompletion::create($data)));
        }

        if ($this->isGranted(Permission::INTERNAL_USER) && $command->getFeeRequired() == 'Y') {
            $data = ['id' => $variation->getId(), 'feeTypeFeeType' => FeeType::FEE_TYPE_VAR];
            $result->merge($this->handleSideEffect(CreateFee::create($data)));
        }

        return $result;
    }
}
