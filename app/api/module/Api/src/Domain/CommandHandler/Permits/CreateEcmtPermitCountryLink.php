<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\EcmtPermitCountryLink;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit Country Link
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermitCountryLink extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'EcmtPermitCountryLink';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command CreateCommand */

        /*$EcmtPermitCountryLink = new EcmtPermitCountryLink();
        $EcmtPermitCountryLink->setEcmtPermitId($ecmtPermit->getId());
        $EcmtPermitCountryLink->setCountryId($country);
        $this->getRepo('EcmtPermitCountryLink')->save($EcmtPermitCountryLink);
        $result = new Result();
        $result->addId('ecmtPermitCountryLink', $ecmtPermit->getId());
        $result->addMessage("ECMT permit country link {$EcmtPermitCountryLink->getId()} created");

        return $result;*/

    }
}
