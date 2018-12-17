<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
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

        /** @var \Dvsa\Olcs\Transfer\Command\Surrender\Update $command */
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

        $this->setContentByStatus($command, $surrender);

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

        if ($command->getCommunityLicenceDocumentInfo() !== null) {
            $surrender->setCommunityLicenceDocumentInfo($command->getCommunityLicenceDocumentInfo());
        }

        $this->getRepo()->save($surrender);

        $this->result->addId('surrender', $surrender->getId());
        $this->result->addMessage('Surrender successfully updated.');

        return $this->result;
    }

    /**
     * @param CommandInterface $command
     * @param SurrenderEntity  $surrender
     *
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function setContentByStatus(CommandInterface $command, SurrenderEntity $surrender): void
    {
        if ($command->getLicenceDocumentStatus()) {
            $licenceDocumentStatus = $this->getRepo()->getRefdataReference($command->getLicenceDocumentStatus());
            $surrender->setLicenceDocumentStatus($licenceDocumentStatus);
        }
        if ($command->getLicenceDocumentInfo() !== null) {
            if ($command->getLicenceDocumentStatus() !== Surrender::SURRENDER_DOC_STATUS_DESTROYED) {
                $surrender->setLicenceDocumentInfo($command->getLicenceDocumentInfo());
            } else {
                $surrender->setLicenceDocumentInfo(null);
            }
        }
    }
}
