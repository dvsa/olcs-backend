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
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * Create Vehicle List Document
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateVehicleListDocument extends AbstractCommandHandler implements
    TransactionedInterface,
    AuthAwareInterface
{
    use AuthAwareTrait;

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

        $user = $command->getUser() ? $command->getUser() : $this->getCurrentUser()->getId();
        $documentId = $this->generateDocument($template, $command, $description, $user);

        $printData = ['documentId' => $documentId, 'jobName' => $description, 'user' => $user];
        $this->result->merge($this->handleSideEffect(Enqueue::create($printData)));

        return $this->result;
    }

    protected function generateDocument($template, $command, $description, $user)
    {
        $dtoData = [
            'template' => $template,
            'query' => [
                'licence' => $command->getId(),
                'user' => $user
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

        return $result->getId('document');
    }
}
