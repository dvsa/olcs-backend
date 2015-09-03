<?php

/**
 * Create PSV Vehicle List Document for discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Discs;

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
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Create PSV Vehicle List Document for discs
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreatePsvVehicleListForDiscs extends AbstractCommandHandler implements
    TransactionedInterface,
    DocumentGeneratorAwareInterface,
    AuthAwareInterface
{
    use DocumentGeneratorAwareTrait;

    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $content = $this->getDocumentGenerator()->generateFromTemplate(
            'PSVVehiclesList',
            [
                'licence' => $command->getId(),
                'user' => $this->getCurrentUser()
            ],
            $command->getKnownValues()
        );

        $file = $this->getDocumentGenerator()->uploadGeneratedContent($content);

        $fileName = (new DateTime())->format('YmdHi') . '_Psv_Vehicle_List.rtf';

        $data = [
            'licence'       => $command->getId(),
            'identifier'    => $file->getIdentifier(),
            'description'   => 'PSV Vehicle List',
            'filename'      => $fileName,
            'category'      => Category::CATEGORY_LICENSING,
            'subCategory'   => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal'    => false,
            'isReadOnly'    => true,
            'size'          => $file->getSize()
        ];

        $printData = ['fileIdentifier' => $file->getIdentifier(), 'jobName' => 'PSV Vehicle List'];
        $this->handleSideEffect(Enqueue::create($printData));

        return $this->handleSideEffect(CreateDocument::create($data));
    }
}
