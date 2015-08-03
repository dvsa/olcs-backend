<?php

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\Grant as Cmd;
use Dvsa\Olcs\Transfer\Command\InspectionRequest\CreateFromGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods as GrantGoodsCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantPsv as GrantPsvCmd;

/**
 * Grant
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Grant extends AbstractCommandHandler implements TransactionedInterface
{
    const ERROR_IR_DUE_DATE = 'APP-GRA-IR-DD-1';
    const ERROR_S4_EMPTY = 'APP-GRA-S4-EMPTY';
    const ERROR_OOOD_UNKNOWN = 'APP-GRA-OOOD-UNKNOWN';
    const ERROR_OORD_UNKNOWN = 'APP-GRA-OORD-UNKNOWN';
    const ERROR_OOOD_NOT_PASSED = 'APP-GRA-OOOD-NOT-PASSED';
    const ERROR_OORD_NOT_PASSED = 'APP-GRA-OORD-NOT-PASSED';

    protected $repoServiceName = 'Application';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        if ($command->getShouldCreateInspectionRequest() === 'Y'
            && $command->getDueDate() === null
        ) {
            throw new ValidationException(
                [
                    'dueDate' => [
                        [self::ERROR_IR_DUE_DATE => 'Due date is required']
                    ]
                ]
            );
        }

        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $errors = array_merge(
            $this->validateS4($application),
            $this->validateOpposition($application)
        );
        if (!empty($errors)) {
            throw new ValidationException($errors);
        }

        if ($application->isGoods()) {
            $result->merge($this->proxyCommand($command, GrantGoodsCmd::class));
        } else {
            $result->merge($this->proxyCommand($command, GrantPsvCmd::class));
        }

        if ($command->getShouldCreateInspectionRequest() == 'Y') {

            $data = [
                'application' => $application->getId(),
                'duePeriod' => $command->getDueDate(),
                'caseworkerNotes' => $command->getNotes()
            ];

            $result->merge($this->handleSideEffect(CreateFromGrant::create($data)));
        }

        return $result;
    }

    /**
     * Validate S4
     *
     * @param ApplicationEntity $application
     *
     * @return array of validation messages
     */
    private function validateS4(ApplicationEntity $application)
    {
        $errors = [];
        // If the there is a schedule 4/1 and the schedule 4/1 status is empty then generate an error
        if ($application->getS4s()->count() > 0) {
            /* @var $s4 \Dvsa\Olcs\Api\Entity\Application\S4 */
            foreach ($application->getS4s() as $s4) {
                if (empty($s4->getOutcome())) {
                    $errors['s4'] = [
                        self::ERROR_S4_EMPTY => 'You must decide the schedule 4/1 before granting the application',
                    ];
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * Validate Oppossition
     *
     * @param ApplicationEntity $application
     *
     * @return array of validation messages
     */
    private function validateOpposition(ApplicationEntity $application)
    {
        // If the Override opposition dates is ticked then do not check the representation/opposition dates
        if ($application->getOverrideOoo() === 'Y') {
            return [];
        }

        $errors = [];
        $oood = $application->getOutOfOppositionDate();
        // Display an additional error if the Out of opposition date is 'Unknown'
        if ($oood === ApplicationEntity::UNKNOWN) {
            $errors['oood'] = [self::ERROR_OOOD_UNKNOWN => 'The out of opposition date cannot be unknown.'];
        }

        $oord = $application->getOutOfRepresentationDate();
        // Display an additional error if the Out of Representation date is 'Unknown'
        if ($oord === ApplicationEntity::UNKNOWN) {
            $errors['oord'] = [self::ERROR_OORD_UNKNOWN => 'The out of representation date cannot be unknown'];
        }

        // Display an additional error if the Out of opposition date is after the current date
        if ($oood instanceof \DateTime && $oood > new \DateTime()
            ) {
            $errors['oood'] = [self::ERROR_OOOD_NOT_PASSED => 'The out of opposition period has not yet passed'];
        }

        // Display an additional error if the Out of representation date is after the current date
        if ($oord instanceof \DateTime && $oord > new \DateTime()
            ) {
            $errors['oord'] = [self::ERROR_OORD_NOT_PASSED => 'The out of representation date has not yet passed'];
        }

        return $errors;
    }
}
