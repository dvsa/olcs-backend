<?php

/**
 * Delete Other Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\OtherLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Delete Other Licence
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class DeleteOtherLicence extends AbstractCommandHandler
{
    protected $repoServiceName = 'OtherLicence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $otherLicence) {
            $this->getRepo()->delete(
                $this->getRepo()->fetchById($otherLicence)
            );

            $result->addId('otherLicence' . $otherLicence, $otherLicence);
            $result->addMessage('Other licence removed');
        }

        return $result;
    }
}
