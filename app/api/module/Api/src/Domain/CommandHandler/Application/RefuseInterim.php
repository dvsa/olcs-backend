<?php

/**
 * Refuse Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\RefuseInterim as Cmd;

/**
 * Refuse Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class RefuseInterim extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface,
    DocumentGeneratorAwareInterface
{
    use AuthAwareTrait,
        DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'Application';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $status = $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_REFUSED);
        $application->setInterimStatus($status);
        $application->setInterimEnd(new DateTime());

        $this->result->addMessage('Interim updated');

        $this->getRepo()->save($application);

        $fileName = $application->isVariation() ? 'GV Refused Interim Direction' : 'GV Refused Interim Licence';

        $file = $this->generateDocument($application);

        $this->result->addMessage('Refuse document generated');

        $data = [
            'identifier' => $file->getIdentifier(),
            'size' => $file->getSize(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'description' => $fileName,
            'filename' => str_replace(' ', '_', $fileName) . '.rtf',
            'isExternal' => false,
            'isScan' => false,
            'licence' => $application->getLicence()->getId(),
            'application' => $application->getId()
        ];

        $this->result->merge($this->handleSideEffect(DispatchDocument::create($data)));

        return $this->result;
    }

    protected function generateDocument(ApplicationEntity $application)
    {
        $type = $application->isVariation() ? 'VAR' : 'NEW';

        $templateName = $type . '_APP_INT_REFUSED';

        $queryData = [
            'user' => $this->getUser()->getId(),
            'licence' => $application->getLicence()->getId(),
            'application' => $application->getId()
        ];

        return $this->getDocumentGenerator()->generateAndStore($templateName, $queryData);
    }
}
