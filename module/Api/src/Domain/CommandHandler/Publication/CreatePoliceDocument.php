<?php

/**
 * Creates the police version of a publication
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Publication\CreateNextPublication as CreateNextPublicationCmd;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as GenerateDocCommand;

/**
 * Creates the police version of a publication
 */
final class CreatePoliceDocument extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Publication';

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PublicationEntity $publication
         * @var CreateNextPublicationCmd
         */
        $publication = $this->getRepo()->fetchUsingId($command);

        return $this->handleSideEffect($this->persistPoliceDoc($publication));
    }

    /**
     * Copies the non-police document, then adds the police data
     *
     * @param PublicationEntity $publication
     * @return GenerateDocCommand
     */
    private function persistPoliceDoc(PublicationEntity $publication)
    {
        $data = [
            'template' => $publication->getDocument()->getId(),
            'query' => [
                'id' => $publication->getId()
            ],
            'description'   => $publication->getDocTemplate()->getDescription() . ' police version',
            'category'      => $publication->getDocTemplate()->getCategory()->getId(),
            'subCategory'   => $publication->getDocTemplate()->getSubCategory()->getId(),
            'isExternal'    => true,
            'isReadOnly'    => 'Y'
        ];

        return GenerateDocCommand::create($data);
    }
}
