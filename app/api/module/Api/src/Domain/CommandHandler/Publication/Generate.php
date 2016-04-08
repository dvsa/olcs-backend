<?php

/**
 * Generate a publication
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Publication\Generate as GenerateCommand;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as GenerateDocCommand;
use Dvsa\Olcs\Api\Domain\Command\Publication\CreateNextPublication as CreateNextPublicationCmd;

/**
 * Generate a publication
 */
final class Generate extends AbstractCommandHandler implements TransactionedInterface
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
         * @var GenerateCommand $command
         */
        $publication = $this->getRepo()->fetchUsingId($command);

        /** @var RefData $generatedPubStatus */
        $generatedPubStatus = $this->getRepo()->getRefdataReference(PublicationEntity::PUB_GENERATED_STATUS);

        //side effects are generating the document, and creating a new publication record for next time
        $sideEffects = [
            $this->getGenerateDocCommand($publication),
            CreateNextPublicationCmd::create(['id' => $publication->getId()])
        ];

        /** @var Result $result */
        $result = $this->handleSideEffects($sideEffects);

        /** @var DocumentEntity $document */
        $document = $this->getRepo()->getReference(DocumentEntity::class, $result->getId('document'));

        $publication->generate($document, $generatedPubStatus);
        $this->getRepo()->save($publication);

        $result->addId('generated_publication', $publication->getId());
        $result->addMessage('Publication was generated');

        return $result;
    }

    /**
     * @param PublicationEntity $publication
     * @return GenerateDocCommand
     */
    private function getGenerateDocCommand(PublicationEntity $publication)
    {
        $data = [
            'template' => $publication->getDocTemplate()->getDocument()->getIdentifier(),
            'query' => [
                'publicationId' => $publication->getId(),
                'pubType' => $publication->getPubType()
            ],
            'description'   => $publication->getDocTemplate()->getDescription() . ' generated',
            'category'      => $publication->getDocTemplate()->getCategory()->getId(),
            'subCategory'   => $publication->getDocTemplate()->getSubCategory()->getId(),
            'isExternal'    => true,
            'isReadOnly'    => 'N'
        ];

        return GenerateDocCommand::create($data);
    }
}
