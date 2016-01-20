<?php

/**
 * Queue a print job
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Queue\Queue;

/**
 * Queue a print job
 *
 */
final class Enqueue extends AbstractCommandHandler implements \Dvsa\Olcs\Api\Domain\AuthAwareInterface
{
    use \Dvsa\Olcs\Api\Domain\AuthAwareTrait;

    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue */

        // Document id parameter must be present and must be a number
        if (!is_numeric($command->getDocumentId())) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException(
                ['Print, document ID parameter must be an integer']
            );
        }

        $user = $this->getCurrentUser();

        // If user has no team, most likely they are selfserve user, in which case we don't know which printer
        // @todo Temporarily stub the printing from these users
        if ($user->getTeam() == null) {

            $document = $this->getRepo()->fetchById($command->getDocumentId());
            /* @var $document \Dvsa\Olcs\Api\Entity\Doc\Document */
            $this->stubPrint($document->getIdentifier(), $command->getJobName());

            $this->result->addMessage(
                "Document '{$document->getIdentifier()}', '{$command->getJobName()}' queued for print"
            );
            return $this->result;
        }

        // check the user does not have at least on printer then fail
        if ($user->getTeam()->getPrinters()->isEmpty()) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\BadRequestException(
                'Failed to generate document as there are no printer settings for the current user'
            );
        }

        $dtoData = [
            'entityId' => $command->getDocumentId(),
            'type' => Queue::TYPE_PRINT,
            'status' => Queue::STATUS_QUEUED,
            'user' => $user->getId(),
            'options' => json_encode(
                [
                    'userId' => $user->getId(),
                    'jobName' => $command->getJobName()
                ]
            ),
        ];
        $this->handleSideEffect(\Dvsa\Olcs\Api\Domain\Command\Queue\Create::create($dtoData));

        $this->result->addMessage(
            "Document id '{$command->getDocumentId()}', '{$command->getJobName()}' queued for print"
        );
        return $this->result;
    }

    /**
     * Stub printing by add a document to licence 7
     *
     * @todo remove this method when stubbing no longer required
     * @codeCoverageIgnore
     *
     * @param string $fileIdentifier
     * @param string $jobName
     */
    private function stubPrint($fileIdentifier, $jobName)
    {
        $document = new \Dvsa\Olcs\Api\Entity\Doc\Document($fileIdentifier);

        $document->setDescription($jobName);
        $document->setFilename(str_replace(' ', '_', $jobName) . '.rtf');
        // hard coded simply so we can demo against *something*
        $document->setLicence($this->getRepo()->getReference(Licence::class, 7));
        $document->setCategory($this->getRepo()->getCategoryReference(Category::CATEGORY_LICENSING));
        $document->setSubCategory(
            $this->getRepo()->getSubCategoryReference(Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST)
        );
        $document->setIsExternal(false);
        $document->setIsReadOnly('Y');
        $document->setIssuedDate(new \Datetime());

        $this->getRepo()->save($document);
    }
}
