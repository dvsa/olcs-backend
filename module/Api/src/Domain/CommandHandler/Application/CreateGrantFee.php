<?php

/**
 * Create Grant Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCmd;

/**
 * Create Grant Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGrantFee extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $this->result->merge($this->createApplicationFee($command->getId()));

        $this->result->merge($this->generateDocument($application));

        return $this->result;
    }

    protected function createApplicationFee($applicationId)
    {
        $data = [
            'id' => $applicationId,
            'feeTypeFeeType' => FeeType::FEE_TYPE_GRANT,
            'description' => 'Grant fee due'
        ];

        return $this->handleSideEffect(CreateApplicationFeeCmd::create($data));
    }

    protected function generateDocument(ApplicationEntity $application)
    {
        $dtoData = [
            'template' => 'FEE_REQ_GRANT_GV',
            'query' => [
                'fee' => $this->result->getId('fee'),
                'application' => $application->getId(),
                'licence' => $application->getLicence()->getId()
            ],
            'description' => 'Goods Grant Fee Request',
            'application' => $application->getId(),
            'licence'     => $application->getLicence()->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_FEE_REQUEST,
            'isExternal'  => false,
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }
}
