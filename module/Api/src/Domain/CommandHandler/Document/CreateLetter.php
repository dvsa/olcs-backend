<?php

/**
 * Create Letter
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as GenerateAndStoreCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Doc\DocTemplate as Entity;
use Dvsa\Olcs\Transfer\Command\Document\CreateLetter as Cmd;

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateLetter extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'DocTemplate';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Entity $template */
        $template = $this->getRepo()->fetchById($command->getTemplate());

        return $this->generateDocument($template, $command);
    }

    protected function generateDocument(DocTemplate $template, Cmd $command)
    {
        $parts = explode('/', $template->getDocument()->getIdentifier());
        $templateName = str_replace('.rtf', '', array_pop($parts));

        $queryData = $command->getData();

        $dtoData = [
            'template' => $templateName,
            'query' => $queryData,
            'description' => $template->getDescription(),
            'category' => $queryData['details']['category'],
            'subCategory' => $queryData['details']['documentSubCategory'],
            'isExternal' => false,
            'isScan' => false,
            'metadata' => $command->getMeta()
        ];

        return $this->handleSideEffect(GenerateAndStoreCmd::create($dtoData));
    }
}
