<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\ContinuationDetail;

use Dvsa\Olcs\Api\Domain\Command\Document\DispatchDocument;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareInterface as DocGenAwareInterface;
use Dvsa\Olcs\Api\Domain\DocumentGeneratorAwareTrait;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail as ContinuationDetailEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Process ContinuationDetail
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Mat Evans <mat.evans@valtech.co.uk> (original Business Service)
 */
final class Process extends AbstractCommandHandler implements TransactionedInterface, DocGenAwareInterface
{
    use DocumentGeneratorAwareTrait;

    protected $repoServiceName = 'ContinuationDetail';

    protected $extraRepos = ['Document'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $continuationDetail = $this->getRepo()->fetchUsingId($command);

        if ($continuationDetail->getStatus()->getId() !== ContinuationDetailEntity::STATUS_PRINTING) {
            $result->addMessage('No op');
            return $result;
        }

        $template = $this->getTemplateName($continuationDetail);

        $storedFile = $this->generateChecklist($continuationDetail, $template);

        $data = [
            'identifier' => $storedFile->getIdentifier(),
            'size' => $storedFile->getSize(),
            'description' => 'Continuation checklist',
            'filename' => $template . '.rtf',
            'licence' => $continuationDetail->getLicence()->getId(),
            'category' => Category::CATEGORY_LICENSING,
            'subCategory' => Category::DOC_SUB_CATEGORY_CONTINUATIONS_AND_RENEWALS_LICENCE,
            'isReadOnly'  => true,
            'isExternal'  => false,
        ];
        $documentResult = $this->handleSideEffect(DispatchDocument::create($data));
        $result->merge($documentResult);

        // update continuation detail record with document id
        $documentId = $documentResult->getId('document');
        $document = $this->getRepo('Document')->fetchById($documentId);
        $continuationDetail->setChecklistDocument($document);
        $this->getRepo()->save($continuationDetail);
        $result
            ->addId('continuationDetail', $continuationDetail->getId())
            ->addMessage('ContinuationDetail updated');

        return $result;
    }

    protected function getTemplateName($continuationDetail)
    {
        $licence = $continuationDetail->getLicence();

        $template = $licence->isGoods() ? 'GV' : 'PSV';

        if ($licence->getLicenceType()->getId() === LicenceEntity::LICENCE_TYPE_SPECIAL_RESTRICTED) {
            $template .= 'SR';
        }

        $template .= 'Checklist';

        return $template;
    }

    /**
     * @param ContinuationDetailEntity $continuationDetail
     * @param string $template template name
     */
    protected function generateChecklist($continuationDetail, $template)
    {
        $licence = $continuationDetail->getLicence();
        $query = [
            'licence' => $licence->getId(),
            'goodsOrPsv' => $licence->getGoodsOrPsv()->getId(),
            'licenceType' => $licence->getLicenceType()->getId(),
            'niFlag' => $licence->getNiFlag(),
            // 'organisation' => $licence->getOrganisation()->getId(),
        ];

        $storedFile = $this->getDocumentGenerator()->generateAndStore($template, $query);

        return $storedFile;
    }
}
