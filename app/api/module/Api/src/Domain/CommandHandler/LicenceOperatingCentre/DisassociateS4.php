<?php

/**
 * DisassociateS4.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceOperatingCentre;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\Application\S4;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Class DisassociateS4
 *
 * Disassociate a licence operating centre with its corresponding S4 record.
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class DisassociateS4 extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'LicenceOperatingCentre';

    public function handleCommand(CommandInterface $command)
    {
        foreach ($command->getLicenceOperatingCentres() as $operatingCentre) {
            /** @var LicenceOperatingCentre $operatingCentre */
            $operatingCentre = $this->getRepo()
                ->getReference(
                    LicenceOperatingCentre::class,
                    $operatingCentre
                );

            $operatingCentre->setS4(null);

            $this->getRepo()->save($operatingCentre);
        }

        $result = new Result();
        $result->addMessage('Operating centre(s) updated.');

        return $result;
    }
}
