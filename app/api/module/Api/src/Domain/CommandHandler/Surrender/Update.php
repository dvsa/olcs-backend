<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Entity\DigitalSignature;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as Cmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

final class Update extends AbstractSurrenderCommandHandler
{

    /**
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var SurrenderEntity $surrender */
        $surrender = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        if ($command->getCommunityLicenceDocumentStatus()) {
            $communityLicDocumentStatus = $this->getRepo()->getRefdataReference($command->getCommunityLicenceDocumentStatus());
            $surrender->setCommunityLicenceDocumentStatus($communityLicDocumentStatus);
        }

        if ($command->getDigitalSignature()) {
            $digitalSignature = $this->getRepo()->getReference(DigitalSignature::class, $command->getDigitalSignature());
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

        $surrender->setDiscDestroyed($command->getDiscDestroyed());
        $surrender->setDiscLost($command->getDiscLost());
        $surrender->setDiscLostInfo($command->getDiscLostInfo());
        $surrender->setDiscStolen($command->getDiscStolen());
        $surrender->setDiscStolenInfo($command->getDiscStolenInfo());

        $this->getRepo()->save($surrender);

        $this->result->addId('surrender', $surrender->getId());
        $this->result->addMessage('Surrender successfully updated.');

        return $this->result;
    }

}
