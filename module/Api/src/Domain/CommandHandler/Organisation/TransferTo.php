<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Organisation;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Transfer an Organisation eg Licences/Application to another organisation
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class TransferTo extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Organisation';
    protected $extraRepos = [
        'Licence',
        'IrfoGvPermit',
        'IrfoPsvAuth',
        'Task',
        'Disqualification',
        'EbsrSubmission',
        'TxcInbox',
        'EventHistory',
        'OrganisationUser',
        'Note'
    ];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $organisationFrom OrganisationFrom */
        $organisationFrom = $this->getRepo()->fetchUsingId($command);

        /* @var $organisationTo OrganisationFrom */
        $organisationTo = $this->getRepo()->fetchById($command->getReceivingOrganisation());

        if ($organisationFrom === $organisationTo) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException('Cannot transfer to same organisation');
        }

        $result->merge($this->transferLicences($organisationFrom, $organisationTo));
        $result->merge($this->transferIrfo($organisationFrom, $organisationTo));
        $result->merge($this->transferDisqualification($organisationFrom, $organisationTo));
        $result->merge($this->transferEbsr($organisationFrom, $organisationTo));
        $result->merge($this->transferEventHistory($organisationFrom, $organisationTo));
        $result->merge($this->transferUsers($organisationFrom, $organisationTo));

        // delete the from organisation
        $this->getRepo()->delete($organisationFrom);

        return $result;
    }

    /**
     * Transfer Licence
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferLicences(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();
        $result->addMessage($organisationFrom->getLicences()->count() .' Licence(s) transferred');
        // get licences for organisationFrom
        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        foreach ($organisationFrom->getLicences() as $licence) {
            $licence->setOrganisation($organisationTo);
            $this->getRepo('Licence')->save($licence);
        }

        return $result;
    }

    /**
     * Transfer Note, IrfoGvPermit, IrfoPsvAuth and Task
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferIrfo(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();

        //set note.irfo_organisation_id = <winning operator id>
        $notes = $this->getRepo('Note')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($notes) .' Note(s) transferred');
        /* @var $note \Dvsa\Olcs\Api\Entity\Note\Note */
        foreach ($notes as $note) {
            $note->setOrganisation($organisationTo);
            $this->getRepo('Note')->save($note);
        }

        //set irfo_gv_permit.organisation_id = <winning operator id>
        $irfoGvPermits = $this->getRepo('IrfoGvPermit')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($irfoGvPermits) .' IrfoGvPermit(s) transferred');
        /* @var $irfoGvPermit \Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit */
        foreach ($irfoGvPermits as $irfoGvPermit) {
            $irfoGvPermit->setOrganisation($organisationTo);
            $this->getRepo('IrfoGvPermit')->save($irfoGvPermit);
        }

        //set irfo_psv_auth.organisation_id = <winning operator id>
        $irfoPsvAuths = $this->getRepo('IrfoPsvAuth')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($irfoPsvAuths) .' IrfoPsvAuth(s) transferred');
        /* @var $irfoPsvAuth \Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth */
        foreach ($irfoPsvAuths as $irfoPsvAuth) {
            $irfoPsvAuth->setOrganisation($organisationTo);
            $this->getRepo('IrfoPsvAuth')->save($irfoPsvAuth);
        }

        //set task.ifo_organisation_id = <winning operator id>
        $tasks = $this->getRepo('Task')->fetchByIrfoOrganisation($organisationFrom);
        $result->addMessage(count($tasks) .' Task(s) transferred');
        /* @var $task \Dvsa\Olcs\Api\Entity\Task\Task */
        foreach ($tasks as $task) {
            $task->setIrfoOrganisation($organisationTo);
            $this->getRepo('Task')->save($task);
        }

        return $result;
    }

    /**
     * Transfer Disqualification
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferDisqualification(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();

        $result->addMessage($organisationFrom->getDisqualifications()->count() .' Disqualifications(s) transferred');
        /* @var $disqualification \Dvsa\Olcs\Api\Entity\Organisation\Disqualification */
        foreach ($organisationFrom->getDisqualifications() as $disqualification) {
            $disqualification->setOrganisation($organisationTo);
            $this->getRepo('Disqualification')->save($disqualification);
        }

        return $result;
    }

    /**
     * Transfer EbsrSubmission and TxcInbox
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferEbsr(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();

        $ebsrSubmissions = $this->getRepo('EbsrSubmission')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($ebsrSubmissions) .' EbsrSubmission(s) transferred');
        /* @var $ebsrSubmission \Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission */
        foreach ($ebsrSubmissions as $ebsrSubmission) {
            $ebsrSubmission->setOrganisation($organisationTo);
            $this->getRepo('EbsrSubmission')->save($ebsrSubmission);
        }

        $txcInboxs = $this->getRepo('TxcInbox')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($txcInboxs) .' TxcInbox(s) transferred');
        /* @var $txcInbox \Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox */
        foreach ($txcInboxs as $txcInbox) {
            $txcInbox->setOrganisation($organisationTo);
            $this->getRepo('TxcInbox')->save($txcInbox);
        }

        return $result;
    }

    /**
     * Transfer EventHistory
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferEventHistory(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();

        $eventHistorys = $this->getRepo('EventHistory')->fetchByOrganisation($organisationFrom);
        $result->addMessage(count($eventHistorys) .' EventHistory(s) transferred');
        /* @var $eventHistory \Dvsa\Olcs\Api\Entity\EventHistory\EventHistory */
        foreach ($eventHistorys as $eventHistory) {
            $eventHistory->setOrganisation($organisationTo);
            $this->getRepo('EventHistory')->save($eventHistory);
        }

        return $result;
    }

    /**
     * Transfer OrganisationUser
     *
     * @param Organisation $organisationFrom
     * @param Organisation $organisationTo
     *
     * @return Result
     */
    protected function transferUsers(Organisation $organisationFrom, Organisation $organisationTo)
    {
        $result = new Result();

        $result->addMessage($organisationFrom->getOrganisationUsers()->count() .' OrganisationUser(s) transferred');
        /* @var $organisationUser \Dvsa\Olcs\Api\Entity\Organisation\OrganisationUser */
        foreach ($organisationFrom->getOrganisationUsers() as $organisationUser) {
            $organisationUser->setOrganisation($organisationTo);
            $this->getRepo('OrganisationUser')->save($organisationUser);
        }

        return $result;
    }
}
