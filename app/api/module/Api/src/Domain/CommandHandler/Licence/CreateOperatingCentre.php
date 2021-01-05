<?php

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CacheAwareInterface;
use Dvsa\Olcs\Api\Domain\CacheAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\Licence\CreateOperatingCentre as Cmd;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Create Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateOperatingCentre extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface, CacheAwareInterface
{
    use AuthAwareTrait;
    use CacheAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = [
        'Document',
        'OperatingCentre',
        'LicenceOperatingCentre'
    ];

    /**
     * @var \Dvsa\Olcs\Api\Domain\Service\OperatingCentreHelper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->helper = $mainServiceLocator->get('OperatingCentreHelper');

        return parent::createService($serviceLocator);
    }

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        if (!$this->isGranted(Permission::INTERNAL_USER)) {
            throw new ForbiddenException();
        }

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchById($command->getLicence());

        $this->helper->validate($licence, $command, $this->isGranted(Permission::SELFSERVE_USER));

        // Create an OC record
        $operatingCentre = $this->helper->createOperatingCentre(
            $command,
            $this->getCommandHandler(),
            $this->result,
            $this->getRepo('OperatingCentre')
        );

        // Link, unlinked documents to the OC
        $this->helper->saveDocuments($licence, $operatingCentre, $this->getRepo('Document'));

        // Create a AOC record
        $this->createLicenceOperatingCentre($licence, $operatingCentre, $command);
        $this->clearLicenceCaches($licence);

        return $this->result;
    }

    /**
     * @param Licence $licence
     * @param OperatingCentre $operatingCentre
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createLicenceOperatingCentre(
        Licence $licence,
        OperatingCentre $operatingCentre,
        Cmd $command
    ) {
        $loc = new LicenceOperatingCentre($licence, $operatingCentre);
        $licence->addOperatingCentres($loc);

        $this->helper->updateOperatingCentreLink(
            $loc,
            $licence,
            $command,
            $this->getRepo('LicenceOperatingCentre')
        );
    }
}
