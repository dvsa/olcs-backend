<?php

/**
 * Create Grant Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface as DocGenAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateApplicationFeeCmd;

/**
 * Create Grant Fee
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateGrantFee extends AbstractCommandHandler implements TransactionedInterface, DocGenAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $result = new Result();

        $result->merge($this->createApplicationFee($command->getId()));

        $params = [
            'fee' => $result->getId('fee'),
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId()
        ];

        $storedFile = $this->getDocumentGenerator()->generateAndStore('FEE_REQ_GRANT_GV', $params);

        $data = [
            'identifier' => $storedFile->getIdentifier(),
            'size' => $storedFile->getSize(),
            'description' => 'Goods Grant Fee Request',
            'filename'    => 'Goods_Grant_Fee_Request.rtf',
            'application' => $application->getId(),
            'licence'     => $application->getLicence()->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_FEE_REQUEST,
            'isExternal'  => false
        ];

        $result->merge($this->handleSideEffect(DispatchDocument::create($data)));

        return $result;
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
}
