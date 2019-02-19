<?php

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateBatch extends AbstractCommandHandler implements TransactionedInterface
{
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
            $template = $this->getTemplateForEntity($application);
        } else {
            $licenceId = $command->getLicence();
            $licence = $this->getRepo('Licence')->fetchById($licenceId);
            $template = $this->getTemplateForEntity($licence);
            $identifier = null;
        }

        $communityLicenceIds = $command->getCommunityLicenceIds();

        foreach ($communityLicenceIds as $id) {
            $query = [
                'licence' => $licence->getId(),
                'communityLic' => $id,
                'application' => $identifier
            ];

            $docId = $this->generateDocument($template, $query);

            $printQueue = EnqueueFileCommand::create(
                [
                    'documentId' => $docId,
                    'jobName' => 'Community Licence'
                ]
            );

            $this->result->merge($this->handleSideEffect($printQueue));

            $this->result->addMessage("Community Licence {$id} processed");
        }

        return $this->result;
    }

    protected function generateDocument($template, $query)
    {
        $dtoData = [
            'template' => $template,
            'query' => $query,
            'description' => 'Community licence',
            'category' => Entity\System\Category::CATEGORY_LICENSING,
            'subCategory' => Entity\System\SubCategory::DOC_SUB_CATEGORY_COMMUNITY_LICENCE,
            'isExternal' => false,
            'isScan' => false
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));

        $this->result->merge($result);

        return $result->getId('document');
    }

    /**
     * @param Entity\Application\Application|Entity\Licence\Licence $entity
     * @return string
     */
    private function getTemplateForEntity($entity)
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
