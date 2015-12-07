<?php

/**
 * Send Ebsr Email Abstract
 *
 * @author Craig R <uk@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repository;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrCancelled as CancelCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRegistered as RegCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrWithdrawn as WithdrawnCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefused as RefusedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrReceived as ReceivedCmd;
use Dvsa\Olcs\Api\Domain\Command\Email\SendEbsrRefreshed as RefreshedCmd;
use Dvsa\Olcs\Api\Entity\Publication\PublicationSection as PublicationSectionEntity;

/**
 * Send Ebsr Email Abstract
 *
 * @author Craig R <uk@valtech.co.uk>
 */
abstract class SendEbsrAbstract extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    const DATE_FORMAT = 'l F jS Y';

    protected $repoServiceName = 'EbsrSubmission';

    protected $template = null;

    /**
     * @param CommandInterface|CancelCmd|RegCmd|WithdrawnCmd|RefusedCmd|ReceivedCmd|RefreshedCmd $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var $repo Repository $repo */
        $repo = $this->getRepo();

        /* @var $ebsr Entity */
        $ebsr = $repo->fetchUsingId($command, Query::HYDRATE_OBJECT, null);
        $busReg = $ebsr->getBusReg();
        $busRegNo = $busReg->getRegNo();

        $orgAddress = $ebsr->getOrganisationEmailAddress();
        $administratorEmails = [];

        //org address will be blank or else validated on ebsr submission
        if (!$orgAddress) {
            $administratorEmails = $ebsr->getOrganisation()->getAdminEmailAddresses();

            if (!empty($administratorEmails)) {
                $orgAddress = $administratorEmails[0];
                unset($administratorEmails[0]);
            }
        }

        $message = new \Dvsa\Olcs\Email\Data\Message($orgAddress, 'email.' . $this->template . '.subject');

        $message->setTranslateToWelsh(
            $busReg->getLicence()->getTranslateToWelsh()
        );

        $message->setSubjectVariables([$busRegNo, $ebsr->getId()]);

        $localAuthoritiesCc = [];
        $localAuthoritiesList = [];
        $localAuthoritiesString = '';

        foreach ($busReg->getLocalAuthoritys() as $localAuth) {
            $localAuthoritiesList[] = $localAuth->getDescription();
            $localAuthoritiesCc[] = $localAuth->getEmailAddress();
        }

        if (!empty($localAuthoritiesList)) {
            $localAuthoritiesString = implode(', ', $localAuthoritiesList);
        }

        $message->setCc(array_merge($localAuthoritiesCc, $administratorEmails));

        $emailData =             [
            'submissionDate' => $this->formatDate($ebsr->getSubmittedDate()),
            'registrationNumber' => $busRegNo,
            'origin' => $busReg->getStartPoint(),
            'destination' => $busReg->getFinishPoint(),
            'lineName' => $busReg->getFormattedServiceNumbers(),
            'startDate' => $this->formatDate($busReg->getEffectiveDate()),
            'localAuthoritys' => $localAuthoritiesString,
            'publicationId' => null
        ];

        if (($command instanceof RegCmd || $command instanceof CancelCmd)) {
            $pubSection = $busReg->getPublicationSectionForGrantEmail();

            if ($pubSection) {
                $pubSectionEntity = $this->getRepo()->getReference(PublicationSectionEntity::class, $pubSection);
                $emailData['publicationId'] = $busReg->getPublicationLinksForGrantEmail($pubSectionEntity);
            }
        }

        $this->sendEmailTemplate($message, $this->template, $emailData);

        $result = new Result();
        $result->addId('ebsrSubmission', $ebsr->getId());
        $result->addMessage('Email sent');

        return $result;
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
