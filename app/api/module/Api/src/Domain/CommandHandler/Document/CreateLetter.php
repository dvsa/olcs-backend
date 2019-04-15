<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Document;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore as GenerateAndStoreCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
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
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Entity $template */
        $template = $this->getRepo()->fetchById($command->getTemplate());

        print_r($template);
        exit();
        return $this->generateDocument($template, $command);
    }

    /**
     * Generate document
     *
     * @param Entity $template template
     * @param Cmd    $command  command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    protected function generateDocument(DocTemplate $template, Cmd $command)
    {
        $queryData = $command->getData();

        $dtoData = [
            'template' => $template->getDocument()->getIdentifier(),
            'query' => $queryData,
            'description' => $template->getDescription(),
            'category' => $queryData['details']['category'],
            'subCategory' => $queryData['details']['documentSubCategory'],
            'isExternal' => false,
            'isScan' => false,
            'metadata' => $command->getMeta()
        ];

        try {
            return  $this->handleSideEffect(GenerateAndStoreCmd::create($dtoData));
        } catch (\Exception $e) {
            throw new ValidationException([$e->getMessage()]);
        }
    }
}
