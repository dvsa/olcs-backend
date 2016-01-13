<?php

/**
 * AccessCorrespondence.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Correspondence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Access Correspondence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class AccessCorrespondence extends AbstractCommandHandler
{
    protected $repoServiceName = 'Correspondence';

    public function handleCommand(CommandInterface $command)
    {
        $correspondence = $this->getRepo()
            ->fetchById(
                $command->getId(),
                \Doctrine\ORM\Query::HYDRATE_OBJECT
            );

        $correspondence->setAccessed('Y');

        $this->getRepo()->save($correspondence);

        $result = new Result();
        $result->addId('correspondence', $correspondence->getId());
        $result->addMessage('Correspondence updated successfully');

        return $result;
    }
}
