<?php

/**
 * Update Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
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
final class UpdateTypeOfLicence extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['TransportManagerLicence', 'ContactDetails', 'GoodsDisc', 'PsvDisc'];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Transfer\Command\Licence\UpdateTypeOfLicence $command command
     *
     * @return Result
     * @throws ForbiddenException
     * @throws RequiresVariationException
     * @throws ValidationException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
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

            $result->merge($this->handleLicenceTypeChangeEffects($licence, $command->getLicenceType()));
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

    /**
     * Handle the side effects of change licence type
     *
     * @param Licence $licence        licence
     * @param string  $newLicenceType New licence type RefData constant
     *
     * @return Result
     */
    private function handleLicenceTypeChangeEffects(Licence $licence, $newLicenceType)
    {
        $result = new Result();
        if ($licence->isStandardNational() && $newLicenceType === Licence::LICENCE_TYPE_RESTRICTED) {
            // -Delink Transport Managers
            $this->delinkTransportManagers($licence);

            // -Remove Establishment address
            $this->removeEstablishmentAddress($licence);
        }

        if ($licence->isStandardInternational() && $newLicenceType === Licence::LICENCE_TYPE_RESTRICTED) {
            // -Delink Transport Managers
            $this->delinkTransportManagers($licence);

            // -Remove Establishment address
            $this->removeEstablishmentAddress($licence);

            if ($licence->isGoods()) {
                // -Anull community licences (Goods only)
                // -Set community licence figure to 0 (Goods only)
                $result->merge($this->returnCommumityLicences($licence));
            }
        }

        if ($licence->isStandardInternational() && $newLicenceType === Licence::LICENCE_TYPE_STANDARD_NATIONAL) {
            // -Anull community licences
            // -Set community licence figure to 0
            $result->merge($this->returnCommumityLicences($licence));
        }

        // -Remove/reissue Discs
        if ($licence->isGoods()) {
            $result->merge($this->reissueGoodsDiscs($licence));
        } else {
            $result->merge($this->reissuePsvDiscs($licence));
        }

        return $result;
    }

    /**
     * Delink Transport Managers
     *
     * @param Licence $licence licence
     *
     * @return void
     */
    private function delinkTransportManagers(Licence $licence)
    {
        foreach ($licence->getTmLicences() as $tml) {
            $this->getRepo('TransportManagerLicence')->delete($tml);
        }
        $licence->getTmLicences()->clear();
    }

    /**
     * Remove the Establishment address from a licence
     *
     * @param Licence $licence licence
     *
     * @return void
     */
    private function removeEstablishmentAddress(Licence $licence)
    {
        if ($licence->getEstablishmentCd()) {
            $this->getRepo('ContactDetails')->delete($licence->getEstablishmentCd());
            $licence->setEstablishmentCd(null);
        }
    }

    /**
     * Return All Community Licences
     *
     * @param Licence $licence licence
     *
     * @return Result
     */
    private function returnCommumityLicences(Licence $licence)
    {
        return $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences::create(['id' => $licence->getId()])
        );
    }

    /**
     * Reissue Goods Discs
     *
     * @param Licence $licence licence
     *
     * @return Result
     */
    private function reissueGoodsDiscs(Licence $licence)
    {
        $ceaseCount = $this->getRepo('GoodsDisc')->ceaseDiscsForLicence($licence->getId());
        $createCount = $this->getRepo('GoodsDisc')->createDiscsForLicence($licence->getId());

        $result = new Result();
        $result->addMessage("{$ceaseCount} goods discs ceased, {$createCount} discs created");

        return $result;
    }

    /**
     * Reissue PSV discs
     *
     * @param Licence $licence licence
     *
     * @return Result
     */
    private function reissuePsvDiscs(Licence $licence)
    {
        $amount = $licence->getPsvDiscsNotCeased()->count();

        $ceaseCount = $this->getRepo('PsvDisc')->ceaseDiscsForLicence($licence->getId());
        $createCount = 0;
        if ($ceaseCount > 0) {
            $createCount = $this->getRepo('PsvDisc')->createPsvDiscs($licence->getId(), $amount);
        }

        $result = new Result();
        $result->addMessage("{$ceaseCount} psv discs ceased, {$createCount} discs created");

        return $result;
    }
}
