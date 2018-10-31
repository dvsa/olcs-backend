<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitRange;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange as RangeEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitRange\Create as CreateRangeCmd;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Create an IRHP Permit Range
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;
    use IrhpPermitRangeOverlapTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitRange';
    protected $extraRepos = ['IrhpPermitStock', 'Country'];

    /**
     * Handle command
     *
     * @param CreateRangeCmd $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command): Result
    {
        if ($this->numberOfOverlappingRanges($command->getIrhpPermitStock(), $command->getFromNo(), $command->getToNo()) !== 0) {
            throw new ValidationException(['This Permit Number Range overlaps with another for this stock']);
        }

        $permitStock = $this->getRepo('IrhpPermitStock')->fetchById($command->getIrhpPermitStock());

        $countrys = [];
        foreach ($command->getRestrictedCountries() as $country) {
            $countrys[] = $this->getRepo('Country')->getReference(Country::class, $country);
        }

        /**
         * @var CreateRangeCmd $command
         */
        $range = RangeEntity::create(
            $permitStock,
            $command->getPrefix(),
            $command->getFromNo(),
            $command->getToNo(),
            $command->getSsReserve(),
            $command->getIsLostReplacement(),
            $countrys
        );

        $this->getRepo()->save($range);

        $this->result->addId('IrhpPermitRange', $range->getId());
        $this->result->addMessage("IRHP Permit Range '{$range->getId()}' created");

        return $this->result;
    }
}
