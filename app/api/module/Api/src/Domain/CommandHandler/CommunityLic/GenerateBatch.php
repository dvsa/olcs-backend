<?php

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocumentSpecific;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateBatch extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'CommunityLic';

    protected $extraRepos = ['Licence', 'Application'];

    public function handleCommand(CommandInterface $command)
    {
        /**
         * @NOTE This check allows us to pass Application whenever possible, but otherwise Licence should be sufficient
         */
        if ($command->getIdentifier() !== null) {
            /** @var Entity\Application\Application $application */
            $application = $this->getRepo('Application')->fetchById($command->getIdentifier());
            $licence = $application->getLicence();
            $identifier = $application->getId();
            $template = $this->getTemplateForLicence($application);
        } else {
            $licenceId = $command->getLicence();
            $licence = $this->getRepo('Licence')->fetchById($licenceId);
            $template = $this->getTemplateForLicence($licence);
            $identifier = null;
        }

        $communityLicenceIds = $command->getCommunityLicenceIds();

        foreach ($communityLicenceIds as $id) {

            $query = [
                'licence' => $licence->getId(),
                'communityLic' => $id,
                'application' => $identifier
            ];

            $documentGenerator = $this->getDocumentGenerator();

            $date = new DateTime();
            $fileName = sprintf(
                '%s_%s-%s-%s_Community_licence.rtf',
                $date->format('YmdHis'),
                $identifier === null ? '0' : $identifier,
                $licence->getId(),
                $id
            );

            $document = $documentGenerator->generateFromTemplate($template, $query);
            $file = $documentGenerator->uploadGeneratedContent($document, 'documents', $fileName);

            $data = [
                'identifier' => $file->getIdentifier(),
                'description' => 'Community licence',
                'filename' => $fileName,
                'category' => Entity\System\Category::CATEGORY_LICENSING,
                'subCategory' => Entity\System\SubCategory::DOC_SUB_CATEGORY_COMMUNITY_LICENCE,
                'isExternal' => false,
                'isScan' => false,
                'size' => $file->getSize()
            ];

            $this->result->merge($this->handleSideEffect(CreateDocumentSpecific::create($data)));

            $printQueue = EnqueueFileCommand::create(
                [
                    'fileIdentifier' => $file->getIdentifier(),
                    // @note not working for now, just migrated, will be implemented in future stories
                    'options' => [PrintSchedulerInterface::OPTION_DOUBLE_SIDED],
                    'jobName' => 'Community Licence'
                ]
            );
            $this->result->merge($this->handleSideEffect($printQueue));

            $this->result->addMessage("Community Licence {$id} processed");
        }

        return $this->result;
    }

    /**
     * @param Entity\Application\Application|Entity\Licence\Licence $entity
     * @return string
     */
    private function getTemplateForLicence($entity)
    {
        if ($entity->isPsv()) {
            $prefix = 'PSV';
        } elseif ($entity->getNiFlag() === 'Y') {
            $prefix = 'GV_NI';
        } else {
            $prefix = 'GV_GB';
        }

        return $prefix . '_European_Community_Licence';
    }
}
