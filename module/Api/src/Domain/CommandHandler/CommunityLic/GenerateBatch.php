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
use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndUploadDocument as GenerateAndUploadDocumentCommand;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\EnqueueFile as EnqueueFileCommand;
use Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler\PrintSchedulerInterface;

/**
 * Generate Batch
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateBatch extends AbstractCommandHandler implements TransactionedInterface
{
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
                'communityLic' => $id
            ];
            if ($identifier) {
                $query['application'] = $identifier;
            }
            
            $processDocument = GenerateAndUploadDocumentCommand::create(
                [
                    'template' => $template,
                    'data'     => $query,
                    'folder'   => 'documents',
                    'fileName' => 'Community Licence'
                ]
            );
                    
            try {
                $processDocResult = $this->getCommandHandler()->handleCommand($processDocument);
            } catch (\Exception $ex) {
                throw new \Exception($ex->getMessage());
            }
            
            $fileId = $processDocResult->getIds()['fileId'];            
            $result->merge($processDocResult);

            $printQueue = EnqueueFileCommand::create(
                [
                    'fileId' => $fileId,
                    'options' => [PrintSchedulerInterface::OPTION_DOUBLE_SIDED],
                    'jobName' => 'Community Licence'
                ]
            );
            try {
                $printQueueResult = $this->getCommandHandler()->handleCommand($printQueue);
            } catch (\Exception $ex) {
                throw new \Exception($ex->getMessage());
            }
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