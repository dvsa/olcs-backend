<?php

/**
 * Print Interim Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface as DocGenAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\PrintInterimDocument as Cmd;

/**
 * Print Interim Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintInterimDocument extends AbstractCommandHandler implements TransactionedInterface, DocGenAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'Application';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $licence = $application->getLicence();

        if ($application->isVariation()) {
            $template = 'GV_INT_DIRECTION_V1';
            $description = 'GV Interim Direction';
        } else {
            $template = 'GV_INT_LICENCE_V1';
            $description = 'GV Interim Licence';
        }

        $query = [
            'application' => $application->getId(),
            'licence' => $licence->getId()
        ];

        $storedFile = $this->getDocumentGenerator()->generateAndStore($template, $query);

        $result->addMessage('Document generated');

        $data = [
            'identifier' => $storedFile->getIdentifier(),
            'size' => $storedFile->getSize(),
            'description' => $description,
            'filename' => str_replace(' ', '_', $description) . '.rtf',
            'application' => $application->getId(),
            'licence' => $licence->getId(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false
        ];

        $result->merge($this->handleSideEffect(DispatchDocument::create($data)));

        return $result;
    }
}
