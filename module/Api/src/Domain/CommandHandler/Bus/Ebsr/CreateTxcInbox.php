<?php

/**
 * Create TxcInbox
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Bus\Ebsr;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Ebsr\TxcInbox;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Bus\BusReg as BusRegEntity;
use Dvsa\Olcs\Api\Entity\Ebsr\EbsrSubmission as EbsrSubmissionEntity;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority as LocalAuthorityEntity;
use Dvsa\Olcs\Api\Domain\Command\Bus\Ebsr\CreateTxcInbox as Cmd;
use Doctrine\ORM\Query;

/**
 * Create TxcInbox
 */
final class CreateTxcInbox extends AbstractCommandHandler
{
    protected $repoServiceName = 'TxcInbox';

    protected $extraRepos = ['Bus'];

    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var Cmd $command
         * @var BusRegEntity $busReg
         * @var EbsrSubmissionEntity $ebsrSubmission
         * @var LocalAuthorityEntity $localAuthority
         */
        $busReg = $this->getRepo('Bus')->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ebsrSubmission = $busReg->getEbsrSubmissions()->first();
        $localAuthorities = $busReg->getLocalAuthoritys();
        $organisation = $ebsrSubmission->getOrganisation();
        $zipDocument = $ebsrSubmission->getDocument();

        $result = new Result();

        foreach ($localAuthorities as $localAuthority) {
            $txc = new TxcInbox($busReg, $zipDocument, $localAuthority);
            $this->getRepo()->save($txc);

            $id = $txc->getId();
            $result->addId('txcInbox_' . $id, $id);
            $result->addMessage('Txc Inbox record created for ' . $localAuthority->getDescription());
        }

        $orgTxc = new TxcInbox($busReg, $zipDocument, null, $organisation);
        $this->getRepo()->save($orgTxc);

        $id = $orgTxc->getId();
        $result->addId('txcInbox_' . $id, $id);
        $result->addMessage('Txc Inbox record created for ' . $organisation->getName());

        return $result;
    }
}
