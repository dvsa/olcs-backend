<?php

/**
 * Refuse Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Transfer\Command\Application\RefuseInterim as Cmd;

/**
 * Refuse Interim
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class RefuseInterim extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @param Cmd $command
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $status = $this->getRepo()->getRefdataReference(ApplicationEntity::INTERIM_STATUS_REFUSED);
        $application->setInterimStatus($status);
        $application->setInterimEnd(new DateTime());

        $this->result->addMessage('Interim updated');

        $this->getRepo()->save($application);

        $this->result->merge($this->generateDocument($application));

        return $this->result;
    }

    protected function generateDocument(ApplicationEntity $application)
    {
        $type = $application->isVariation() ? 'VAR' : 'NEW';

        $description = $application->isVariation() ? 'GV Refused Interim Direction' : 'GV Refused Interim Licence';

        $dtoData = [
            'template' => $type . '_APP_INT_REFUSED',
            'query' => [
                'licence' => $application->getLicence()->getId(),
                'application' => $application->getId()
            ],
            'description' => $description,
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal' => false,
            'isScan' => false,
            'licence' => $application->getLicence()->getId(),
            'application' => $application->getId(),
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }
}
