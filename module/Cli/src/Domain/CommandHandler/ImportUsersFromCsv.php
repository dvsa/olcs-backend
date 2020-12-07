<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Service\OpenAm;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Validators\EmailAddress;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * @author Dmitry Golubev <dmitrij.golubev@valtech.com>
 */
class ImportUsersFromCsv extends AbstractCommandHandler
{
    const CNT_FIELDS_IN_ROW = 6;

    const ERR_FILE_404 = 'Source CSV file not found by path %s';
    const ERR_FILE_CANT_OPEN = 'Source CSV file can\'t be open for read (%s)';
    const ERR_RES_FILE_CANT_OPEN = 'Result CSV file can\'t be open for write (%s)';

    const CSV_ERR_USER_TYPE_INVALID = 'User type (%s) is not "%s"';
    const CSV_ERR_USER_ALREADY_IN_DB = 'Cant find unique username in db after %d tries';
    const CSV_ERR_USER_NOT_CREATED_IN_DB = 'User not created in DB. Fail with error "%s"';
    const CSV_ERR_USER_NOT_CREATED_IN_OPENAM = 'User not created in OpenAm. Fail with error "%s"';
    const CSV_ERR_USER_ROLE_INVALID = 'Invalid role "%s"';
    const CSV_ERR_USER_TEAM_INVALID = 'Invalid team name "%s"';
    const CSV_ERR_EMAIL_INVALID = 'Invalid email "%s"';
    const CSV_ERR_INVALID_ROW = 'Row in csv file is empty or contains an incorrect count of elements';

    protected $repoServiceName = 'User';
    protected $extraRepos = ['Team', 'Role'];

    /** @var  string */
    private $path;

    /** @var  OpenAm\User */
    private $openAmSrv;
    /** @var  OpenAm\ClientInterface */
    private $openAmClient;
    /** @var  Repository\TransactionManager */
    private $transMngr;

    /** @var Repository\User */
    private $userRepo;
    /** @var Repository\Team */
    private $teamRepo;
    /** @var Repository\Role */
    private $roleRepo;

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\ImportUsersFromCsv $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $this->path = trim($command->getCsvPath());

        $this->userRepo = $this->getRepo();
        $this->teamRepo = $this->getRepo('Team');
        $this->roleRepo = $this->getRepo('Role');

        //  process CSV
        if (is_file($this->path) === false) {
            throw new \Exception(sprintf(self::ERR_FILE_404, $this->path));
        }

        //  define path to result file if not provided
        $pathRes = $command->getResultCsvPath();
        if ($pathRes === null) {
            $pathParts = pathinfo($this->path);

            $pathRes = $pathParts['dirname'] . DIRECTORY_SEPARATOR . $pathParts['filename'] . '_result.csv';
        }

        $this->result->addMessage('Result is stored at ' . $pathRes);

        $fhTrg = @fopen($pathRes, "wb+");
        if ($fhTrg === false) {
            throw new \Exception(sprintf(self::ERR_RES_FILE_CANT_OPEN, $pathRes));
        }

        $fhSrc = @fopen($this->path, "rb");
        if ($fhSrc === false) {
            throw new \Exception(sprintf(self::ERR_FILE_CANT_OPEN, $this->path));
        }

        //  copy titles to result file
        $firstRow = fgetcsv($fhSrc);
        if (is_array($firstRow) && current($firstRow) !== null) {
            $titles = array_merge($firstRow, ['loginId', 'password', 'status']);
            fputcsv($fhTrg, $titles);
        }

        //  process rows
        $idx = 1;
        while (($line = fgetcsv($fhSrc)) !== false) {
            $res = $this->processUserFromCsv($line);

            $this->result->addMessage('row ' . $idx++ . ': ' . $res['status']);

            fputcsv($fhTrg, $res);
        }

        fclose($fhSrc);
        fclose($fhTrg);

        return $this->result;
    }

    /**
     * Process user line from CSV
     *
     * @param array $row User data from csv
     *
     * @return array
     */
    private function processUserFromCsv(array $row)
    {
        if (count($row) !== self::CNT_FIELDS_IN_ROW) {
            $row['status'] = self::CSV_ERR_INVALID_ROW;
            return $row;
        }

        list($type, $teamName, $roleName, $firstName, $surName, $email) = $row;

        $userName = preg_replace('/[^a-zA-Z0-9.]/u', '-', ($firstName . '.' . $surName));
        $password = $this->openAmSrv->generatePassword();

        $row = [
            'userType' => $type,
            'teamName' => $teamName,
            'roleName' => $roleName,
            'firstName' => $firstName,
            'surName' => $surName,
            'email' => $email,
            'loginId' => $userName,
            'password' => $password,
            'status' => 'OK',
        ];

        //  check email
        if (!(new EmailAddress())->isValid($email)) {
            $row['status'] = sprintf(self::CSV_ERR_EMAIL_INVALID, $email);
            return $row;
        }

        //  check type
        if ($type !== Entity\User\User::USER_TYPE_INTERNAL) {
            $row['status'] = sprintf(self::CSV_ERR_USER_TYPE_INVALID, $type, Entity\User\User::USER_TYPE_INTERNAL);
            return $row;
        }

        //  check role
        $role = $this->roleRepo->fetchByRole($roleName);
        if ($role === null) {
            $row['status'] = sprintf(self::CSV_ERR_USER_ROLE_INVALID, $roleName);
            return $row;
        }

        //  check team
        $team = current($this->teamRepo->fetchByName($teamName)) ?: null;
        if ($team === null) {
            $row['status'] = sprintf(self::CSV_ERR_USER_TEAM_INVALID, $teamName);
            return $row;
        }

        // validate username
        $userName = $this->userRepo->findUserNameAvailable($userName);
        if ($userName === null) {
            $row['status'] = sprintf(self::CSV_ERR_USER_ALREADY_IN_DB, Repository\User::USERNAME_GEN_TRY_COUNT);
            return $row;
        }

        $row['loginId'] = $userName;

        $pid = $this->openAmSrv->generatePid($userName);

        //  create user in DB
        $this->transMngr->beginTransaction();

        try {
            $person = (new Entity\Person\Person())
                ->updatePerson($firstName, $surName);

            $contactDetails =
                (new Entity\ContactDetails\ContactDetails(
                    $this->getRepo()->getRefdataReference(Entity\ContactDetails\ContactDetails::CONTACT_TYPE_USER)
                ))
                ->setEmailAddress($email)
                ->setPerson($person);

            $data = [
                'loginId' => $userName,
                'roles' => [$role],
                'team' => $team,
            ];
            $user = Entity\User\User::create($pid, $type, $data);
            $user->setContactDetails($contactDetails);

            $this->getRepo()->save($user);
        } catch (\Exception $e) {
            $this->transMngr->rollback();

            $row['status'] = sprintf(self::CSV_ERR_USER_NOT_CREATED_IN_DB, $e->getMessage());
            return $row;
        }

        //  create user in Open AM
        try {
            $this->openAmClient->registerUser(
                $userName,
                $pid,
                $email,
                $surName,
                $firstName,
                OpenAm\Client::REALM_INTERNAL,
                $password
            );
        } catch (OpenAm\FailedRequestException $e) {
            $this->transMngr->rollback();

            $row['status'] = sprintf(
                self::CSV_ERR_USER_NOT_CREATED_IN_OPENAM,
                preg_replace('/[\n\r]/m', '', $e->getMessage())
            );
            return $row;
        }

        $this->transMngr->commit();

        return $row;
    }

    /**
     * Create service
     *
     * @param \Dvsa\Olcs\Api\Domain\CommandHandlerManager $sm Service Manager
     *
     * @return AbstractCommandHandler|\Dvsa\Olcs\Api\Domain\CommandHandler\TransactioningCommandHandler
     */
    public function createService(ServiceLocatorInterface $sm)
    {
        /** @var ServiceLocatorInterface $sl */
        $sl = $sm->getServiceLocator();

        $this->openAmSrv = $sl->get(OpenAm\UserInterface::class);
        $this->openAmClient = $sl->get(OpenAm\ClientInterface::class);
        $this->transMngr = $sl->get('TransactionManager');

        return parent::createService($sm);
    }
}
