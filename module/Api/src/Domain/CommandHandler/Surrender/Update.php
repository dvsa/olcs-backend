<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

final class Update extends AbstractSurrenderCommandHandler
{
    /**
     * @param CommandInterface $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SurrenderEntity $surrender */
        $surrender = $this->getRepo()->fetchOneByLicenceId($command->getId(), Query::HYDRATE_OBJECT);

        if ($command->getCommunityLicenceDocumentStatus()) {
            $communityLicDocumentStatus = $this->getRepo()->getRefdataReference($command->getCommunityLicenceDocumentStatus());
            $surrender->setCommunityLicenceDocumentStatus($communityLicDocumentStatus);
        }

        if ($command->getDigitalSignature()) {
            $digitalSignature = $this->getRepo()->getReference(
                DigitalSignature::class,
                $command->getDigitalSignature()
            );
            $surrender->setDigitalSignature($digitalSignature);
        }

        if ($command->getLicenceDocumentStatus()) {
            $licenceDocumentStatus = $this->getRepo()->getRefdataReference($command->getLicenceDocumentStatus());
            $surrender->setLicenceDocumentStatus($licenceDocumentStatus);
        }

        if ($command->getStatus()) {
            $status = $this->getRepo()->getRefdataReference($command->getStatus());
            $surrender->setStatus($status);
        }

        if ($command->getDiscDestroyed() !== null) {
            $surrender->setDiscDestroyed($command->getDiscDestroyed());
        }

        if ($command->getDiscLost() !== null) {
            $surrender->setDiscLost($command->getDiscLost());
        }

        if ($command->getDiscLostInfo() !== null) {
            $surrender->setDiscLostInfo($command->getDiscLostInfo());
        }

        if ($command->getDiscStolen() !== null) {
            $surrender->setDiscStolen($command->getDiscStolen());
        }

        if ($command->getDiscStolenInfo() !== null) {
            $surrender->setDiscStolenInfo($command->getDiscStolenInfo());
        }

        if ($command->getSignatureType() !== null) {
            $surrender->setSignatureType($command->getSignatureType());
        }

        if ($command->getLicenceDocumentInfo() !== null) {
            $surrender->setLicenceDocumentInfo($command->getLicenceDocumentInfo());
        }

        if ($command->getCommunityLicenceDocumentInfo() !== null) {
            $surrender->setCommunityLicenceDocumentInfo($command->getCommunityLicenceDocumentInfo());
        }

        $this->getRepo()->save($surrender);

        $this->result->addId('surrender', $surrender->getId());
        $this->result->addMessage('Surrender successfully updated.');

        return $this->result;
    }
}
