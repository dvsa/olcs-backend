<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as StockRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitJurisdiction\Create as CreateDevolvedQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\IrhpPermitSector\Create as CreateSectorQuotasCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as StockEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitStock\Create as CreateStockCmd;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Create an IRHP Permit Stock
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface, ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitStock';

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var StockRepo      $stockRepo
         * @var CreateStockCmd $command
         */
        $stockRepo = $this->getRepo('IrhpPermitStock');

        $permitType = $stockRepo->getReference(IrhpPermitType::class, $command->getPermitType());

        $stock = StockEntity::create(
            $permitType,
            $command->getValidFrom(),
            $command->getValidTo(),
            $command->getInitialStock(),
            $this->getRepo()->getRefDataReference(StockEntity::STATUS_SCORING_NEVER_RUN)
        );

        try {
            $stockRepo->save($stock);
        } catch (\Exception $e) {
            throw new ValidationException(['You cannot create a duplicate stock']);
        }

        $stockId = $stock->getId();

        $sideEffects = [
            CreateSectorQuotasCmd::create(['id' => $stockId]),
            CreateDevolvedQuotasCmd::create(['id' => $stockId])
        ];

        $this->result->merge(
            $this->handleSideEffects($sideEffects)
        );

        $this->result->addId('IrhpPermitStock', $stockId);
        $this->result->addMessage("IRHP Permit Stock '{$stockId}' created");

        return $this->result;
    }
}
