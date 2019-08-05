<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Email\SendEcmtShortTermSuccessful;
use Dvsa\Olcs\Api\Domain\Command\Fee\CreateFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Fee\Fee;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\GrantabilityChecker;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplication as SubmitApplicationCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Command Handler to action the granting of an IrhpApplication
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class Grant extends AbstractCommandHandler implements ToggleRequiredInterface, TransactionedInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['FeeType'];

    /** @var GrantabilityChecker */
    private $grantabilityChecker;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->grantabilityChecker = $mainServiceLocator->get('PermitsGrantabilityChecker');

        return parent::createService($serviceLocator);
    }


    /**
     * Handle command
     *
     * @param SubmitApplicationCmd|CommandInterface $command command
     *
     * @return Result
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        if (!$this->grantabilityChecker->isGrantable($irhpApplication)) {
            throw new ForbiddenException('Insufficient permit availability to grant this application');
        }

        if (!$irhpApplication->canBeGranted()) {
            throw new ForbiddenException('This application cannot be granted');
        }

        $irhpApplication->grant($this->refData(IrhpInterface::STATUS_AWAITING_FEE));

        $this->getRepo()->save($irhpApplication);

        $this->result->merge(
            $this->handleSideEffects(
                [
                    $this->getCreateFeeCommand($irhpApplication),
                    $this->emailQueue(
                        SendEcmtShortTermSuccessful::class,
                        ['id' => $irhpApplication->getId()],
                        $irhpApplication->getId()
                    )
                ]
            )
        );

        $this->result->addMessage('IRHP application granted');
        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }

    /**
     * @param IrhpApplication $irhpApplication
     * @return CreateFee
     */
    private function getCreateFeeCommand(IrhpApplication $irhpApplication)
    {
        $totalRequired = $irhpApplication->getFirstIrhpPermitApplication()->getTotalEmissionsCategoryPermitsRequired();
        $feeType = $this->getRepo('FeeType')->getLatestByProductReference($irhpApplication->getIssueFeeProductReference());
        return CreateFee::create(
            [
                'licence' => $irhpApplication->getLicence()->getId(),
                'irhpApplication' => $irhpApplication->getId(),
                'invoicedDate' => date("Y-m-d"),
                'description' => $feeType->getDescription(),
                'feeType' => $feeType->getId(),
                'feeStatus' => Fee::STATUS_OUTSTANDING,
                'quantity' => $totalRequired,
            ]
        );
    }
}
