<?php

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\GoodsDisc;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as PrintDiscsCommand;
use Dvsa\Olcs\Transfer\Command\Licence\CreateVehicleListDocument as CreateVehicleListDocumentCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc as GoodsDiscEntity;
use Dvsa\Olcs\Api\Entity\System\DiscSequence as DiscSequenceEntity;

/**
 * Print goods discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PrintDiscs extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'GoodsDisc';

    protected $extraRepos = ['DiscSequence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $discsToPrint = $this->getRepo()->fetchDiscsToPrint(
            $command->getNiFlag(), $command->getLicenceType()
        );
        $this->validateParameters(
            $command->getStartNumber(),
            $discsToPrint,
            $command->getLicenceType(),
            $command->getDiscSequence()
        );

        $data = [
            'discs' => $discsToPrint,
            'type' => 'Goods',
            'startNumber' => $command->getStartNumber()
        ];
        $printDiscs = PrintDiscsCommand::create($data);
        $printDiscsResult = $this->handleSideEffect($printDiscs);
        $result->merge($printDiscsResult);
        $result->addMessage("Goods discs printed");

        $result->merge($this->createVehicleLists($discsToPrint));

        $this->getRepo()->setIsPrintingOn($discsToPrint);

        return $result;
    }

    protected function createVehicleLists($discsToPrint)
    {
        $result = new Result();
        $licences = [];
        foreach ($discsToPrint as $disc) {
            $licenceId = $disc->getLicenceVehicle()->getLicence()->getId();
            $licences[$licenceId] = [
                'id' => $licenceId,
                'type' => 'dp'
            ];
        }
        foreach ($licences as $data) {
            $generateVehicleList = CreateVehicleListDocumentCommand::create($data);
            $this->handleSideEffect($generateVehicleList);
            $result->addMessage('Vehicle list generated for licence ' . $data['id']);
        }
        return $result;
    }

    protected function validateParameters($startNumberEntered, $discsToPrint, $licenceType, $discSequence)
    {
        if (!$discsToPrint) {
            throw new ValidationException([GoodsDiscEntity::ERROR_NO_DISCS_TO_PRINT => 'No discs to print']);
        }
        $discSequence = $this->getRepo('DiscSequence')->fetchById($discSequence);
        $startNumber = $discSequence->getDiscNumber($licenceType);
        if ($startNumberEntered < $startNumber) {
            throw new ValidationException(
                [
                    'startNumber' => [
                        DiscSequenceEntity::ERROR_DECREASING => 'Decreasing the start number is not permitted'
                    ]
                ]
            );
        }
    }
}
