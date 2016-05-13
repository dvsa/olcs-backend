<?php

/**
 * Print Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Print Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintLicence extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        return $this->generateDocument($licence);
    }

    protected function generateDocument(LicenceEntity $licence)
    {
        $template = $this->getTemplateName($licence);

        $description = $this->getDescription($licence);

        $dtoData = [
            'template' => $template,
            'query' => [
                'licence' => $licence->getId()
            ],
            'description' => $description,
            'licence'     => $licence->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isExternal'  => false,
            'dispatch' => true
        ];

        return $this->handleSideEffect(GenerateAndStore::create($dtoData));
    }

    private function getTemplateName(LicenceEntity $licence)
    {
        if ($licence->isGoods()) {
            return 'GV_LICENCE_V1';
        }

        if ($licence->isSpecialRestricted()) {
            return 'PSVSRLicence';
        }

        return 'PSV_LICENCE_V1';
    }

    private function getDescription(LicenceEntity $licence)
    {
        if ($licence->isGoods()) {
            return 'GV Licence';
        }

        if ($licence->isSpecialRestricted()) {
            return 'PSV-SR Licence';
        }

        return 'PSV Licence';
    }
}
