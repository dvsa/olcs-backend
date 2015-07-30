<?php

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\Exception\RequiresVariationException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateTypeOfLicence extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // If we are not trying to update the licence type
        if ($licence->getLicenceType() === $this->getRepo()->getRefdataReference($command->getLicenceType())) {
            $result->addMessage('No updates required');
            return $result;
        }

        if (!$this->isGranted(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence)) {
            throw new ForbiddenException('You do not have permission to update type of licence');
        }

        if (!$licence->canBecomeSpecialRestricted()
            && $command->getLicenceType() === Licence::LICENCE_TYPE_SPECIAL_RESTRICTED
        ) {
            throw new ValidationException(
                [
                    'licenceType' => [
                        Licence::ERROR_CANT_BE_SR => 'You are not able to change licence type to special restricted'
                    ]
                ]
            );
        }

        // Internally we don't need a variation
        if ($this->isGranted(Permission::INTERNAL_USER)) {

            $this->handleLicenceTypeChangeEffects($licence, $command->getLicenceType());
            $licence->setLicenceType($this->getRepo()->getRefdataReference($command->getLicenceType()));

            $this->getRepo()->save($licence);
            $result->addMessage('Licence saved successfully');
            return $result;
        }

        throw new RequiresVariationException(
            'Updating the type of licence section requires a variation',
            Licence::ERROR_REQUIRES_VARIATION
        );
    }

    private function handleLicenceTypeChangeEffects(Licence $licence, $newLicenceType)
    {
        if ($licence->isStandardNational() && $newLicenceType === Licence::LICENCE_TYPE_RESTRICTED) {
            // -Delink Transport Managers
            // @todo HOW? delete all TransportManagerLicence records

            // -Remove Establishment address
            $licence->setEstablishmentCd(null);

            // -Set large vehicle authority to 0 (PSV only)
            if ($licence->isPsv()) {
                // @todo is the right property?
                $licence->setTotAuthLargeVehicles(0);
            }
        }

        if ($licence->isStandardInternational() && $newLicenceType === Licence::LICENCE_TYPE_RESTRICTED) {
            // -Delink Transport Managers

            // -Remove Establishment address
            $licence->setEstablishmentCd(null);

            // -Set large vehicle authority to 0 (PSV only)
            if ($licence->isPsv()) {
                // @todo is the right property?
                $licence->setTotAuthLargeVehicles(0);
            }
            if ($licence->isGoods()) {
                // -Anull community licences (Goods only)
                // @todo HOW Is this the same as VoidAllCommunityLicences
                // -Set community licence figure to 0 (Goods only)
                // @todo HOW where is this number stored
            }
        }

        if ($licence->isStandardInternational() && $newLicenceType === Licence::LICENCE_TYPE_STANDARD_NATIONAL) {
                // -Anull community licences
                // -Set community licence figure to 0
        }

        // -Remove/reissue Discs
        // @todo HOW is this the same as cease?
    }
}
