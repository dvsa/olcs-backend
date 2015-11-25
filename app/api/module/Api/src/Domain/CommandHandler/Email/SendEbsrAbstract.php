<?php

/**
 * Send Transport Manager Application Email
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repository;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as Entity;
use Dvsa\Olcs\Transfer\Query\Ebsr\EbsrSubmission as EbsrSubmissionQuery;
use Dvsa\Olcs\Transfer\Command\Ebsr\SubmissionCreate as SubmissionCreateCommand;

/**
 * Send Ebsr Email Abstract
 *
 * @author Craig R <uk@valtech.co.uk>
 */
abstract class SendEbsrAbstract extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\EmailAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\EmailAwareTrait;

    protected $repoServiceName = 'EbsrSubmission';

    protected $template = null;

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        // This doesn't exist yet, so in the UT, I'll create a mock of CommandInterface.
        /** @var SubmissionCreateCommand $command */

        /** @var $repo Repository $repo */
        $repo = $this->getRepo();

        $ebsrSubmissionQuery = EbsrSubmissionQuery::create(
            ['id' => $command->getId()]
        );
        /* @var $ebsr Entity */
        $ebsr = $repo->fetchUsingId($ebsrSubmissionQuery, Query::HYDRATE_OBJECT, null);

        $message = new \Dvsa\Olcs\Email\Data\Message(
            $ebsr->getOrganisationEmailAddress(),
            'email.' . $this->template . '.subject'
        );
        $message->setTranslateToWelsh(
            $ebsr->getBusReg()->getLicence()->getTranslateToWelsh()
        );

        $localAuthoritiesList = [];
        $localAuthoritiesString = '';

        foreach ($ebsr->getBusReg()->getLocalAuthoritys() as $localAuth) {
            $localAuthoritiesList[] = $localAuth->getDescription();
        }

        if (!empty($localAuthoritiesList)) {
            $localAuthoritiesString = implode(', ', $localAuthoritiesList) . '.';
        }

        $this->sendEmailTemplate(
            $message,
            $this->template,
            [
                'submissionDate' => date('d/m/Y', strtotime($ebsr->getSubmittedDate())),
                'registrationNumber' => $ebsr->getBusReg()->getRegNo(),
                'origin' => $ebsr->getBusReg()->getStartPoint(),
                'destination' => $ebsr->getBusReg()->getFinishPoint(),
                'lineName' => $ebsr->getBusReg()->getServiceNo(), //
                'startDate' => date('d/m/Y', strtotime($ebsr->getBusReg()->getEffectiveDate())),
                'localAuthoritys' => $localAuthoritiesString,
                'publicationId' => '[PUBLICATION_ID]', //
            ]
        );

        $result = new Result();
        $result->addId('ebsrSubmission', $ebsr->getId());
        $result->addMessage('Email sent');

        return $result;
    }
}
