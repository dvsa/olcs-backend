<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as WindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Create as CreateWindowCmd;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Create an IRHP Permit Window
 *
 * @author Andy Newton
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use IrhpPermitWindowOverlapTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitStock'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var CreateWindowCmd $command
         */
        // If there are overlapping windows.
        if ($this->numberOfOverlappingWindows($command->getIrhpPermitStock(), $command->getStartDate(), $command->getEndDate()) > 0) {
            throw new ValidationException(['The dates overlap with another window for this Permit stock']);
        }

        /** @var IrhpPermitStock $irhpPermitStock */
        $irhpPermitStock = $this->getRepo('IrhpPermitStock')->fetchById($command->getIrhpPermitStock());

        if ($irhpPermitStock->getIrhpPermitType()->isEcmtAnnual()
            && $command->getEmissionsCategory() == IrhpPermitWindow::EMISSIONS_CATEGORY_NA_REF) {
            throw new ValidationException(['Emissions Category: N/A not valid for Annual ECMT Stock']);
        }

        $emissionsCategory = $this->refData($command->getEmissionsCategory());

        $window = WindowEntity::create(
            $irhpPermitStock,
            $emissionsCategory,
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getDaysForPayment()
        );

        $this->getRepo()->save($window);

        $this->result->addId('IrhpPermitWindow', $window->getId());
        $this->result->addMessage("IRHP Permit Window '{$window->getId()}' created");

        return $this->result;
    }
}
