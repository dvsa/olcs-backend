<?php

/**
 * Enqueue File
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrintScheduler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Document\CreateDocument as CreateDocumentCommand;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;

/**
 * Enqueue File
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class EnqueueFile extends AbstractCommandHandler
{
    protected $repoServiceName = 'Document';

    public function handleCommand(CommandInterface $command)
    {
        // Currently, this is just stub, we are calling CreateDocument command
        // to save file instead of printing

        $result = new Result();

        $data = [
            'identifier'    => $command->getFileId(),
            'description'   => $command->getJobName(),
            'filename'      => str_replace(' ', '_', $command->getJobName()) . '.rtf',
            'licence'       => 7, // hard coded simply so we can demo against *something*
            'category'      => CategoryEntity::CATEGORY_LICENSING,
            'subCategory'   => CategoryEntity::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal'    => false,
            'isReadOnly'    => true,
            // @TODO date helper needed
            // $this->getServiceLocator()->get('Helper\Date')->getDate('Y-m-d H:i:s'),
            'issuedDate'    => date('Y-m-d H:i:s'),
            // @TODO need to implement
            // $file->getSize()
            'size'          => 1000 // hard coded
        ];

        $createDocument = CreateDocumentCommand::create($data);
        $createDocumentResult = $this->getCommandHandler()->handleCommand($createDocument);

        $result->merge($createDocumentResult);
        $result->addId('file', $command->getFileId());
        $result->addMessage('File printed');

        return $result;
    }
}
