<?php

/**
 * Update Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Impounding;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Impounding;
use Dvsa\Olcs\Transfer\Command\Cases\Impounding\DeleteImpounding as Cmd;

/**
 * Delete Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class DeleteImpounding extends AbstractCommandHandler
{
    protected $repoServiceName = 'Impounding';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        try {
            $this->getRepo()->beginTransaction();

            $impounding = $this->createImpoundingObject($command);

            $this->getRepo()->delete($impounding);
            $this->getRepo()->commit();

            $result->addMessage('Impounding deleted');

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * @param Cmd $command
     * @return Impounding
     */
    private function createImpoundingObject(Cmd $command)
    {
        $impounding = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        return $impounding;
    }
}
