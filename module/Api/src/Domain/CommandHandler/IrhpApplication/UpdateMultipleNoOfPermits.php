<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\GenerateApplicationFee as GenerateApplicationFeeCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\RegenerateIssueFee as RegenerateIssueFeeCmd;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateMultipleNoOfPermits as UpdateMultipleNoOfPermitsCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\MaxStockPermitsByApplication;
use RuntimeException;

/**
 * Update multiple no of permits
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class UpdateMultipleNoOfPermits extends AbstractCommandHandler implements
    ToggleRequiredInterface,
    TransactionedInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitApplication'];

    /**
     * Handle command
     *
     * @param UpdateMultipleNoOfPermitsCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplicationId = $command->getId();
        $irhpApplicationRepo = $this->getRepo();
        $irhpApplication = $irhpApplicationRepo->fetchById($irhpApplicationId);

        if (!$irhpApplication->isReadyForNoOfPermits()) {
            throw new ForbiddenException('IRHP application is not ready for number of permits');
        }

        $irhpApplication->storePermitsRequired();

        $irhpPermitApplicationRepo = $this->getRepo('IrhpPermitApplication');
        $rows = $irhpPermitApplicationRepo->getByIrhpApplicationWithStockInfo($command->getId());
        $commandCountries = $command->getPermitsRequired();

        $response = $this->handleQuery(
            MaxStockPermitsByApplication::create(['id' => $irhpApplicationId])
        );
        $maxStockPermits = $response['result'];

        foreach ($rows as $row) {
            $countryId = $row['countryId'];
            $validToTimestamp = strtotime($row['validTo']);
            $year = date('Y', $validToTimestamp);
            $maxPermits = $maxStockPermits[$row['stockId']];

            if ($maxPermits > 0) {
                if (isset($commandCountries[$countryId][$year]) && is_numeric($commandCountries[$countryId][$year])) {
                    $permitsRequired = intval($commandCountries[$countryId][$year]);
                    if (($permitsRequired < 0) || ($permitsRequired > $maxPermits)) {
                        throw new RuntimeException(
                            sprintf(
                                'Out of range data for country %s in year %s - expected range 0 to %d but received %d',
                                $countryId,
                                $year,
                                $maxPermits,
                                $permitsRequired
                            )
                        );
                    }
                } else {
                    throw new RuntimeException(
                        sprintf('Missing data or incorrect type for country %s in year %s', $countryId, $year)
                    );
                }
            } else {
                $permitsRequired = 0;
            }

            $irhpPermitApplication = $row['irhpPermitApplication'];
            $irhpPermitApplication->updatePermitsRequired($permitsRequired);
            $irhpPermitApplicationRepo->saveOnFlush($irhpPermitApplication);
        }


        $irhpApplication->resetCheckAnswersAndDeclaration();
        $irhpApplicationRepo->saveOnFlush($irhpApplication);
        $irhpPermitApplicationRepo->flushAll();

        $feeCommands = [
            GenerateApplicationFeeCmd::create(['id' => $irhpApplicationId])
        ];

        if ($irhpApplication->hasPermitsRequiredChanged()) {
            $feeCommands[] = RegenerateIssueFeeCmd::create(['id' => $irhpApplicationId]);
        }

        $this->result->merge(
            $this->handleSideEffects($feeCommands)
        );

        $this->result->addId('irhpApplication', $irhpApplicationId);
        $this->result->addMessage(
            sprintf(
                'Updated %d required permit counts for IRHP application',
                count($rows)
            )
        );

        return $this->result;
    }
}
