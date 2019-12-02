<?php

/**
 * Update User
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\User;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Email\SendUserTemporaryPassword as SendUserTemporaryPasswordDto;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractUserCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistory;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Update User
 */
final class UpdateUser extends AbstractUserCommandHandler implements
    AuthAwareInterface,
    TransactionedInterface,
    OpenAmUserAwareInterface
{
    use AuthAwareTrait,
        OpenAmUserAwareTrait;

    const RESET_PASSWORD_BY_POST = 'post';
    const RESET_PASSWORD_BY_EMAIL = 'email';

    protected $repoServiceName = 'User';

    protected $extraRepos = ['Application', 'ContactDetails', 'Licence', 'EventHistory', 'EventHistoryType'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::CAN_MANAGE_USER_INTERNAL)) {
            throw new ForbiddenException('You do not have permission to manage the record');
        }

        $data = $command->getArrayCopy();

        if (($command->getUserType() === User::USER_TYPE_OPERATOR) && (!empty($data['licenceNumber']))) {
            // fetch licence by licence number
            $licence = $this->getRepo('Licence')->fetchByLicNo($data['licenceNumber']);

            // link with the organisation
            $data['organisations'] = [$licence->getOrganisation()];
        } elseif (($command->getUserType() === User::USER_TYPE_TRANSPORT_MANAGER) && (!empty($data['application']))) {
            // fetch application by id
            $application = $this->getRepo('Application')->fetchWithLicenceAndOrg($data['application']);

            // link with the organisation
            $data['organisations'] = [$application->getLicence()->getOrganisation()];
        }
        /** @var User $user */
        $user = $this->getRepo()->fetchById($command->getId(), Query::HYDRATE_OBJECT, $command->getVersion());

        // validate username
        $this->validateUsername($data['loginId'], $user->getLoginId());

        // validate roles
        $this->validateRoles($data['roles'], $user->getRoles()->toArray());

        $user->update(
            $this->getRepo()->populateRefDataReference($data)
        );

        if ($user->getContactDetails() instanceof ContactDetails) {
            // update existing contact details
            $user->getContactDetails()->update(
                $this->getRepo('ContactDetails')->populateRefDataReference(
                    $command->getContactDetails()
                )
            );
        } else {
            // create new contact details
            $user->setContactDetails(
                ContactDetails::create(
                    $this->getRepo()->getRefdataReference(ContactDetails::CONTACT_TYPE_USER),
                    $this->getRepo('ContactDetails')->populateRefDataReference(
                        $command->getContactDetails()
                    )
                )
            );
        }

        $this->getRepo()->save($user);

        $this->getOpenAmUser()->updateUser(
            $user->getPid(),
            $user->getLoginId(),
            $command->getContactDetails()['emailAddress'],
            ($command->getAccountDisabled() === 'Y') ? true : false
        );

        $result = new Result();
        $result->addId('user', $user->getId());
        $result->addMessage('User updated successfully');

        if ($command->getResetPassword() !== null) {
            $result->merge(
                $this->resetPassword($user, $command->getResetPassword())
            );
        }

        return $result;
    }

    /**
     * Generates a new temp password and sends letter or email
     *
     * @param User   $user User
     * @param string $mode Mode
     *
     * @return Result
     */
    private function resetPassword(User $user, $mode)
    {
        $licence = null;

        if ($mode === self::RESET_PASSWORD_BY_POST) {
            $org = $user->getRelatedOrganisation();

            if (!($org instanceof Organisation)) {
                throw new ValidationException(['ERR_RESET_PASS_BY_POST_NO_ADDRESS']);
            }

            // find a licence related to an organisation the user belongs to
            $licence = $org->getRelatedLicences()->first();

            if (!($licence instanceof Licence)) {
                throw new ValidationException(['ERR_RESET_PASS_BY_POST_NO_ADDRESS']);
            }
        }

        $password = null;

        $this->getOpenAmUser()->resetPassword(
            $user->getPid(),
            function ($params) use (&$password) {
                $password = $params['password'];
            }
        );

        $result = new Result();
        $result->addMessage('Temporary password successfully generated and saved');

        $eventData = null;

        switch ($mode) {
            case self::RESET_PASSWORD_BY_POST:
                // send a letter with the temp password
                $result->merge(
                    $this->sendLetter($licence, $password)
                );
                $eventData = 'By post';
                break;
            case self::RESET_PASSWORD_BY_EMAIL:
                // send temporary password email
                $result->merge(
                    $this->handleSideEffect(
                        SendUserTemporaryPasswordDto::create(
                            [
                                'user' => $user->getId(),
                                'password' => $password,
                            ]
                        )
                    )
                );
                $eventData = 'By email';
                break;
        }

        // create event history record
        $eventHistory = new EventHistory(
            $this->getUser(),
            $this->getRepo('EventHistoryType')->fetchOneByEventCode(EventHistoryType::EVENT_CODE_PASSWORD_RESET),
            $eventData
        );
        $eventHistory->setAccount($user);

        $this->getRepo('EventHistory')->save($eventHistory);

        return $result;
    }

    /**
     * Sends letter with a temporary password
     *
     * @param Licence $licence  Licence
     * @param string  $password Password
     *
     * @return Result
     */
    private function sendLetter(Licence $licence, $password)
    {
        $template = 'SELF_SERVICE_NEW_PASSWORD';

        $queryData = [
            'licence' => $licence->getId()
        ];

        $knownValues = [
            'SELF_SERVICE_PASSWORD' => $password,
        ];

        $documentId = $this->generateDocument($template, $queryData, $knownValues);

        $printQueue = EnqueueFileCommand::create(
            [
                'documentId' => $documentId,
                'jobName' => 'New temporary password'
            ]
        );

        return $this->handleSideEffect($printQueue);
    }

    /**
     * Generates a reset password document and returns its id
     *
     * @param string $template    Template
     * @param array  $queryData   Query data
     * @param array  $knownValues Known values
     *
     * @return int
     */
    private function generateDocument($template, $queryData, $knownValues)
    {
        $dtoData = [
            'template' => $template,
            'query' => $queryData,
            'knownValues' => $knownValues,
            'description' => 'Reset password letter',
            'category' => CategoryEntity::CATEGORY_APPLICATION,
            'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_APPLICATION_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));

        return $result->getId('document');
    }
}
