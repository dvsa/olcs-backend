<?php

/**
 * Send Ebsr Email Abstract
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repository;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrCancelled as CancelCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRegistered as RegCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrWithdrawn as WithdrawnCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefused as RefusedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrReceived as ReceivedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefreshed as RefreshedCmd;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;
use Dvsa\Olcs\Api\Entity\View\BusRegSearchView as BusRegSearchViewEntity;
use Dvsa\Olcs\Email\Data\Message;

/**
 * Send Ebsr Email Abstract
 *
 * @author Craig R <uk@valtech.co.uk>
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
abstract class SendEbsrAbstract extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    const DATE_FORMAT = 'l F jS Y';

    const UNKNOWN_REG_NO = 'unknown reg no';
    const UNKNOWN_SERVICE_NO = 'unknown service no';
    const UNKNOWN_START_POINT = 'unknown start point';
    const UNKNOWN_FINISH_POINT = 'unknown finish point';
    const UNKNOWN_START_DATE = 'unknown start date';

    protected $repoServiceName = 'EbsrSubmission';

    protected $extraRepos = ['BusRegSearchView'];

    protected $template = null;

    /**
     * @var EbsrSubmissionEntity
     */
    protected $ebsr;

    /**
     * @var BusRegEntity
     */
    protected $busReg;

    /**
     * @var BusRegEntity
     */
    protected $submissionResult;

    /**
     * @var array
     */
    protected $emailData;

    /**
     * @var
     */
    protected $regNo;

    /**
     * @param CommandInterface|CancelCmd|RegCmd|WithdrawnCmd|RefusedCmd|ReceivedCmd|RefreshedCmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var $repo Repository $repo */
        $repo = $this->getRepo();
        $this->ebsr = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT, null);
        $this->busReg = $this->ebsr->getBusReg();
        $this->submissionResult = $this->decodeEbsrSubmissionResult($this->ebsr->getEbsrSubmissionResult());

        //get template variables
        $this->emailData = $this->getTemplateVariables($command);

        //get the bus regNo (which could come from a number of sources)
        $this->regNo = $this->getBusRegNo();

        $message = $this->getMessage();

        $this->sendEmailTemplate($message, $this->template, $this->emailData);

        $result = new Result();
        $result->addId('ebsrSubmission', $this->ebsr->getId());
        $result->addMessage('Email sent');

        return $result;
    }

    /**
     * Gets the bus registration number, works differently depending on where we get the data from
     *
     * @return null|string
     */
    private function getBusRegNo()
    {
        //if the submission failed, we won't always have a bus reg
        if ($this->busReg instanceof BusRegEntity) {
            return $this->busReg->getRegNo();
        } elseif ($this->emailData['registrationNumber'] !== self::UNKNOWN_REG_NO) {
            return $this->emailData['registrationNumber'];
        }

        return null;
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        $localAuthoritiesCc = [];

        //if the submission failed, we won't always have a bus reg
        if ($this->busReg instanceof BusRegEntity) {
            //get local auth data (list of local auths copied, and their email addresses)
            $localAuthoritiesCc = $this->getLocalAuthEmails($this->busReg->getLocalAuthoritys());
        }

        //organisation address
        $orgAddress = $this->ebsr->getOrganisationEmailAddress();

        //administrator emails
        $administratorEmails = $this->ebsr->getOrganisation()->getAdminEmailAddresses();

        //org address will be blank or else validated on ebsr submission
        if (!$orgAddress) {
            $orgAddress = $administratorEmails[0];
            unset($administratorEmails[0]);
        }

        //get the email message, and assign addresses
        $message = new Message($orgAddress, $this->getSubject());
        $message->setCc(array_merge($localAuthoritiesCc, $administratorEmails));

        //email subject line
        $message->setSubjectVariables($this->getSubjectVars());

        return $message;
    }

    /**
     * Gets subject line variables, depends on whether the bus reg is included or not
     *
     * @return array
     */
    private function getSubjectVars()
    {
        if ($this->regNo) {
            return [$this->regNo, $this->ebsr->getId()];
        }

        return [$this->ebsr->getId()];
    }

    /**
     * Decides on the subject line. If template is a string then it is easy to match it. Failure emails use an array
     * of templates, so we do it based on whether we have a bus reg entity
     *
     * @return string
     */
    private function getSubject()
    {
        $subject = 'email.ebsr-failed-no-bus-reg.subject';

        if (!is_array($this->template)) {
            $subject = 'email.' . $this->template . '.subject';
        } elseif ($this->busReg instanceof BusRegEntity) {
            $subject = 'email.ebsr-failed.subject';
        }

        return $subject;
    }

    /**
     * Gets template variables. If we have no busReg, then will proxy to getTemplateVariablesNoBusReg
     *
     * @param CommandInterface $command
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function getTemplateVariables($command)
    {
        //if the submission failed, we probably won't have a bus reg
        if (!$this->busReg instanceof BusRegEntity) {
            return $this->getTemplateVariablesNoBusReg();
        }

        /** @var BusRegSearchViewEntity $formattedServiceNumbers */
        $formattedServiceNumbers = $this->getRepo('BusRegSearchView')->fetchById($this->busReg->getId());

        $emailData = [
            'submissionDate' => $this->formatDate($this->ebsr->getSubmittedDate()),
            'registrationNumber' => $this->busReg->getRegNo(),
            'origin' => $this->busReg->getStartPoint(),
            'destination' => $this->busReg->getFinishPoint(),
            'lineName' => $formattedServiceNumbers->getServiceNo(),
            'startDate' => $this->formatDate($this->busReg->getEffectiveDate()),
            'localAuthoritys' => $this->getLocalAuthString($this->busReg->getLocalAuthoritys()),
            'submissionErrors' => isset($this->submissionResult['errors']) ? $this->submissionResult['errors'] : [],
            'hasBusData' => true,
            'publicationId' => null
        ];

        //when ebsr bus reg is registered or cancelled, we send additional publication data
        if (($command instanceof RegCmd || $command instanceof CancelCmd)) {
            $pubSection = $this->busReg->getPublicationSectionForGrantEmail();

            if ($pubSection) {
                $pubSectionEntity = $this->getRepo()->getReference(PublicationSectionEntity::class, $pubSection);
                $emailData['publicationId'] = $this->busReg->getPublicationLinksForGrantEmail($pubSectionEntity);
            }
        }

        return $emailData;
    }

    /**
     * Get template variables when we have no bus reg
     *
     * @return array
     */
    private function getTemplateVariablesNoBusReg()
    {
        $rawData = $this->submissionResult['raw_data'];

        //set some defaults
        $hasBusData = false;
        $regNo = self::UNKNOWN_REG_NO;
        $serviceNo = self::UNKNOWN_SERVICE_NO;
        $origin = self::UNKNOWN_START_POINT;
        $destination = self::UNKNOWN_FINISH_POINT;
        $startDate = self::UNKNOWN_START_DATE;

        //check for a reg no
        if (isset($rawData['licNo']) && isset($rawData['routeNo'])) {
            $hasBusData = true;
            $regNo = $rawData['licNo'] . '/' . $rawData['routeNo'];
        }

        //check service no
        if (isset($rawData['serviceNo'])) {
            $hasBusData = true;
            $serviceNo = $rawData['serviceNo'];

            if (isset($rawData['otherServiceNumbers']) && is_array($rawData['otherServiceNumbers'])) {
                $serviceNo .= '(' . implode(',', $rawData['otherServiceNumbers']) .')';
            }
        }

        //check start point
        if (isset($rawData['startPoint'])) {
            $hasBusData = true;
            $origin = $rawData['startPoint'];
        }

        //check finish point
        if (isset($rawData['finishPoint'])) {
            $hasBusData = true;
            $destination = $rawData['finishPoint'];
        }

        //check effective date
        if (isset($rawData['effectiveDate'])) {
            $hasBusData = true;
            $startDate = $this->formatDate($rawData['effectiveDate']);
        }

        return [
            'submissionDate' => $this->formatDate($this->ebsr->getSubmittedDate()),
            'submissionErrors' => $this->submissionResult['errors'],
            'registrationNumber' => $regNo,
            'origin' => $origin,
            'destination' => $destination,
            'lineName' => $serviceNo,
            'startDate' => $startDate,
            'hasBusData' => $hasBusData
        ];
    }

    /**
     * returns the unserialized version of $ebsrSubmissionResult
     *
     * @param string $ebsrSubmissionResult
     * @return array
     */
    private function decodeEbsrSubmissionResult($ebsrSubmissionResult)
    {
        return unserialize($ebsrSubmissionResult);
    }

    /**
     * Returns array of local authority emails to cc
     *
     * @param $localAuths
     * @return array
     */
    private function getLocalAuthEmails($localAuths)
    {
        $localAuthoritiesCc = [];

        /** @var LocalAuthority $localAuth */
        foreach ($localAuths as $localAuth) {
            $localAuthoritiesCc[] = $localAuth->getEmailAddress();
        }

        return $localAuthoritiesCc;
    }

    /**
     * Returns a comma separated list of local authorities
     *
     * @param $localAuths
     * @return string
     */
    private function getLocalAuthString($localAuths)
    {
        $localAuthoritiesList = [];

        /** @var LocalAuthority $localAuth */
        foreach ($localAuths as $localAuth) {
            $localAuthoritiesList[] = $localAuth->getDescription();
        }

        return implode(', ', $localAuthoritiesList);
    }

    /**
     * @param string|\DateTime $date
     * @return string
     */
    private function formatDate($date)
    {
        if (!$date instanceof \DateTime) {
            return date(self::DATE_FORMAT, strtotime($date));
        }

        return $date->format(self::DATE_FORMAT);
    }
}
