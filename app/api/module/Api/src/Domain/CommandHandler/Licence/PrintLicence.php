<?php

/**
 * Print Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface as DocGenAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Print Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class PrintLicence extends AbstractCommandHandler implements TransactionedInterface, DocGenAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        $template = $this->getTemplateName($licence);

        $description = $this->getDescription($licence);

        $storedFile = $this->getDocumentGenerator()
            ->generateAndStore($template, ['licence' => $command->getId()]);

        $result->addMessage('Document generated');

        $data = [
            'identifier'  => $storedFile->getIdentifier(),
            'description' => $description,
            'filename'    => str_replace(' ', '_', $description) . '.rtf',
            'licence'     => $command->getId(),
            'category'    => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_OTHER_DOCUMENTS,
            'isReadOnly'  => true,
            'isExternal'  => false
        ];

        $result->merge($this->handleSideEffect(DispatchDocument::create($data)));

        return $result;
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
