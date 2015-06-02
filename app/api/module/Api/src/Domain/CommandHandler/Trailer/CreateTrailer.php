<?php

/**
 * Create Trailer
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Trailer;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Trailer;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create Trailer
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class CreateTrailer extends AbstractCommandHandler
{
    protected $repoServiceName = 'Trailer';

    protected $licenceRepo = null;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->licenceRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Licence');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $licence = $this->licenceRepo->fetchById($command->getLicence());

        $trailer = new Trailer();
        $trailer->setTrailerNo($command->getTrailerNo());
        $trailer->setSpecifiedDate(new \DateTime($command->getSpecifiedDate()));
        $trailer->setLicence($licence);

        $this->getRepo()->save($trailer);

        $result = new Result();
        $result->addId('trailer', $trailer->getId());
        $result->addMessage('Trailer created successfully');

        return $result;
    }
}
