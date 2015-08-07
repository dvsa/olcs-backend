<?php

/**
 * Create Letter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific as CreateDocumentSpecificCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as Entity;
use Dvsa\Olcs\Transfer\Command\Document\CreateLetter as Cmd;

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateLetter extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'DocTemplate';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $queryData = $command->getData();

        /** @var Entity $template */
        $template = $this->getRepo()->fetchById($command->getTemplate());

        $docId = $template->getDocument()->getIdentifier();

        // Swap spaces for underscores
        $identifier = str_replace(' ', '_', $docId);

        $content = $this->getDocumentGenerator()->generateFromTemplateIdentifier($identifier, $queryData);
        $fileName = date('YmdHis') . '_' . $this->formatFilename($template->getDescription()) . '.rtf';
        $file = $this->getDocumentGenerator()->uploadGeneratedContent($content, null, $fileName);

        $data = [
            'identifier' => $file->getIdentifier(),
            'description' => $template->getDescription(),
            'filename' => $fileName,
            'category' => $queryData['details']['category'],
            'subCategory' => $queryData['details']['documentSubCategory'],
            'isExternal' => false,
            'isScan' => false,
            'metadata' => $command->getMeta(),
            'size' => $file->getSize()
        ];

        $this->result->merge($this->handleSideEffect(CreateDocumentSpecificCmd::create($data)));
        $this->result->addMessage('File created');

        return $this->result;
    }

    private function formatFilename($input)
    {
        return str_replace([' ', '/'], '_', $input);
    }
}
