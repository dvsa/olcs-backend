<?php

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Document\CreateDocument;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateVehicleListDocument extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface,
    AuthAwareInterface
{
    use DocumentGeneratorAwareTrait,
        AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        if ($command->getType() === 'dp') {
            $template = 'GVDiscLetter';
            $description = 'New disc notification';
        } else {
            $template = 'GVVehiclesList';
            $description = 'Goods Vehicle List';
        }

        $file = $this->getDocumentGenerator()->generateAndStore(
            $template,
            [
                'licence' => $command->getId(),
                'user' => $this->getCurrentUser()
            ]
        );

        $fileName = date('YmdHi') . '_Goods_Vehicle_List.rtf';

        $data = [
            'licence'       => $command->getId(),
            'identifier'    => $file->getIdentifier(),
            'description'   => $description,
            'filename'      => $fileName,
            'category'      => Category::CATEGORY_LICENSING,
            'subCategory'   => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal'    => false,
            'isReadOnly'    => true,
            'size'          => $file->getSize()
        ];

        $printData = ['fileIdentifier' => $file->getIdentifier(), 'jobName' => $description];
        $this->handleSideEffect(Enqueue::create($printData));

        return $this->handleSideEffect(CreateDocument::create($data));
    }
}
