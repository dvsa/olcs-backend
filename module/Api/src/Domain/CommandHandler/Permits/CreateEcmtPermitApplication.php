<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermitApplication extends AbstractCommandHandler implements
    ToggleRequiredInterface,
    TransactionedInterface
{
    use ToggleAwareTrait;

    const LICENCE_INVALID_MSG = 'Licence ID %s with number %s is unable to make an ECMT application';

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitWindow', 'Licence'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var LicenceEntity                  $licence
         * @var CreateEcmtPermitApplicationCmd $command
         */
        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());

        if (!$licence->canMakeEcmtApplication()) {
            $message = sprintf(self::LICENCE_INVALID_MSG, $licence->getId(), $licence->getLicNo());
            throw new ForbiddenException($message);
        }

        /** @var CreateEcmtPermitApplicationCmd $ecmtPermitApplication */
        $ecmtPermitApplication = $this->createPermitApplicationObject($licence);

        $this->getRepo()->save($ecmtPermitApplication);

        $this->result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $this->result->addMessage('ECMT Permit Application created successfully');

        $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByIrhpPermitType(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
            new DateTime(),
            Query::HYDRATE_OBJECT,
            $command->getYear()
        );

        $this->result->merge(
            $this->handleSideEffect(
                CreateIrhpPermitApplication::create(
                    [
                        'window' => $window->getId(),
                        'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
                    ]
                )
            )
        );

        return $this->result;
    }

    /**
     * Create EcmtPermitApplication object
     *
     * @param LicenceEntity $licence licence
     *
     * @return EcmtPermitApplication
     */
    private function createPermitApplicationObject(LicenceEntity $licence): EcmtPermitApplication
    {
        return EcmtPermitApplication::createNew(
            $this->getRepo()->getRefDataReference(IrhpInterface::SOURCE_SELFSERVE),
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED),
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::PERMIT_TYPE),
            $licence,
            date('Y-m-d')
        );
    }
}
