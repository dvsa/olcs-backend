<?php

/**
 * Print PSV discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PsvDisc;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Discs\PrintDiscs as PrintDiscsCommand;
use Dvsa\Olcs\Api\Domain\Command\Discs\CreatePsvVehicleListForDiscs as CreatePsvVehicleListForDiscsCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc as PsvDiscEntity;
use Dvsa\Olcs\Api\Entity\System\DiscSequence as DiscSequenceEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Print PSV discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class PrintDiscs extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'PsvDisc';

    protected $extraRepos = ['DiscSequence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $discsToPrint = $this->getRepo()->fetchDiscsToPrint(
            $command->getLicenceType()
        );
        $this->validateParameters(
            $command->getStartNumber(),
            $discsToPrint,
            $command->getLicenceType(),
            $command->getDiscSequence()
        );

        $data = [
            'discs' => $discsToPrint,
            'type' => 'PSV',
            'startNumber' => $command->getStartNumber()
        ];
        $printDiscs = PrintDiscsCommand::create($data);
        $printDiscsResult = $this->handleSideEffect($printDiscs);
        $result->merge($printDiscsResult);
        $result->addMessage("PSV discs printed");

        $result->merge($this->createVehicleLists($discsToPrint));

        $this->getRepo()->setIsPrintingOn($discsToPrint);

        return $result;
    }

    protected function createVehicleLists($discsToPrint)
    {
        $result = new Result();
        $queries = [];
        $bookmarks = [];
        foreach ($discsToPrint as $disc) {
            $licenceId = $disc->getLicence()->getId();
            if (!isset($bookmarks[$licenceId])) {
                $bookmarks[$licenceId] = [
                    'NO_DISCS_PRINTED' => ['count' => 0]
                ];
            }
            $bookmarks[$licenceId]['NO_DISCS_PRINTED']['count'] ++;
            $queries[$licenceId] = [
                'id' => $licenceId
            ];
        }
        foreach ($queries as $licenceId => $data) {
            $data['knownValues'] = $bookmarks[$licenceId];
            $generateVehicleList = CreatePsvVehicleListForDiscsCommand::create($data);
            $this->handleSideEffect($generateVehicleList);
            $result->addMessage('Vehicle list generated for licence ' . $licenceId);
        }

        return $result;
    }

    protected function validateParameters($startNumberEntered, $discsToPrint, $licenceType, $discSequence)
    {
        if (!$discsToPrint) {
            throw new ValidationException([PsvDiscEntity::ERROR_NO_DISCS_TO_PRINT => 'No discs to print']);
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
