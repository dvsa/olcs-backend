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

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateVehicleListDocument extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $content = $this->getDocumentGenerator()->generateFromTemplate(
            'GVVehiclesList',
            [
                'licence' => $command->getId()
            ]
        );

        $file = $this->getDocumentGenerator()->uploadGeneratedContent($content);

        $fileName = date('YmdHi') . '_Goods_Vehicle_List.rtf';

        $data = [
            'licence'       => $command->getId(),
            'identifier'    => $file->getIdentifier(),
            'description'   => 'Goods Vehicle List',
            'filename'      => $fileName,
            'category'      => Category::CATEGORY_LICENSING,
            'subCategory'   => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal'    => false,
            'isReadOnly'    => true,
            'size'          => $file->getSize()
        ];

        $printData = ['fileIdentifier' => $file->getIdentifier(), 'jobName' => 'Goods Vehicle List'];
        $this->handleSideEffect(Enqueue::create($printData));

        return $this->handleSideEffect(CreateDocument::create($data));
    }
}
