<?php

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\EnqueueFile as EnqueueFileCommand;
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

    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $licenceId = $command->getLicence();
        $licence = $this->getRepo('Licence')->fetchById($licenceId);
        $communityLicenceIds = $command->getCommunityLicenceIds();
        $identifier = $command->getIdentifier();

        foreach ($communityLicenceIds as $id) {
            $template = $this->getTemplateForLicence($licence);

            $query = [
                'licence' => $licenceId,
                'communityLic' => $id,
                'application' => $identifier
            ];

            $documentGenerator = $this->getDocumentGenerator();

            $document = $documentGenerator->generateFromTemplate($template, $query);
            $file = $documentGenerator->uploadGeneratedContent($document, 'documents');

            $printQueue = EnqueueFileCommand::create(
                [
                    'fileId' => $file->getIdentifier(),
                    'options' => [PrintSchedulerInterface::OPTION_DOUBLE_SIDED],
                    'jobName' => 'Community Licence'
                ]
            );
            $printQueueResult = $this->handleSideEffect($printQueue);
            $result->merge($printQueueResult);

            $result->addMessage("Community Licence {$id} processed");
        }

        return $result;
    }

    private function getTemplateForLicence($licence)
    {
        $prefix = '';

        if ($licence->getGoodsOrPsv()->getId() === LicenceEntity::LICENCE_CATEGORY_PSV) {
            $prefix = 'PSV';
        } elseif ($licence->getNiFlag() === 'Y') {
            $prefix = 'GV_NI';
        } else {
            $prefix = 'GV_GB';
        }

        return $prefix . '_European_Community_Licence';
    }
}
