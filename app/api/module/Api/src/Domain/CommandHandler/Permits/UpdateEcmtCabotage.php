<?php
/**
 * Created by IntelliJ IDEA.
 * Date: 26/07/2018
 * Time: 15:00
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtCabotage as UpdateEcmtCabotageCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Doctrine\ORM\Query;

/**
 * Update ECMT Euro 6
 *
 * @author ONE
 */
final class UpdateEcmtCabotage extends AbstractCommandHandler
{
    protected $repoServiceName = 'EcmtPermitApplication';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var $ecmtApplication EcmtPermitApplication
         * @var $command UpdateEcmtCabotageCmd
         */
        $ecmtApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $ecmtApplication->setCabotage($command->getCabotage());

        $this->getRepo()->save($ecmtApplication);
        $result->addId('cabotage', $ecmtApplication->getId());
        $result->addMessage('ECMT Permit Application cabotage updated');
        return $result;
    }
}
