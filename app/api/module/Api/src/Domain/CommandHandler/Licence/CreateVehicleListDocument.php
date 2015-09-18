<?php

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\PrintScheduler\Enqueue;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateVehicleListDocument extends AbstractCommandHandler implements TransactionedInterface
{
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

        $identifier = $this->generateDocument($template, $command, $description);

        $printData = ['fileIdentifier' => $identifier, 'jobName' => $description];
        $this->result->merge($this->handleSideEffect(Enqueue::create($printData)));

        return $this->result;
    }

    protected function generateDocument($template, $command, $description)
    {
        $dtoData = [
            'template' => $template,
            'query' => [
                'licence' => $command->getId()
            ],
            'licence'       => $command->getId(),
            'description'   => $description,
            'category'      => Category::CATEGORY_LICENSING,
            'subCategory'   => Category::DOC_SUB_CATEGORY_LICENCE_VEHICLE_LIST,
            'isExternal'    => false,
            'isReadOnly'    => true
        ];

        $result = $this->handleSideEffect(GenerateAndStore::create($dtoData));

        $this->result->merge($result);

        return $result->getId('identifier');
    }
}
