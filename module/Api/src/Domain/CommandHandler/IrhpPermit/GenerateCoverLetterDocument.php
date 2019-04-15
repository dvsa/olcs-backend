<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Doc\Document as DocumentEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Generate Cover Letter Document for IRHP Permit
 */
final class GenerateCoverLetterDocument extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * @var array
     */
    private $templates = [
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT
            => EcmtPermitApplicationEntity::PERMIT_COVERING_LETTER_TEMPLATE_NAME,
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL
            => DocumentEntity::IRHP_PERMIT_ANN_BILAT_COVERING_LETTER,
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL
            => DocumentEntity::IRHP_PERMIT_ANN_MULTILAT_COVERING_LETTER,
    ];

    /**
     * Handle command
     *
     * @param CommandInterface $command Command
     *
     * @return Result
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpPermit = $this->getRepo()->fetchById($command->getIrhpPermit(), Query::HYDRATE_OBJECT);

        // get document template
        $template = $this->getTemplate($irhpPermit);

        $description = sprintf(
            '%s %d',
            strtoupper(str_replace('_', ' ', $template)),
            $irhpPermit->getId()
        );

        $document = $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => $template,
                    'query' => $this->getDocumentQuery($irhpPermit),
                    'knownValues' => [],
                    'description' => $description,
                    'category' => CategoryEntity::CATEGORY_PERMITS,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT_COVERING_LETTER,
                    'isExternal' => false,
                    'isScan' => false
                ]
            )
        );

        $this->result->addId('coveringLetter', $document->getId('document'), true);
        $this->result->addMessage($description . ' RTF created and stored');

        return $this->result;
    }

    /**
     * Get template
     *
     * @param IrhpPermitEntity $irhpPermit IRHP Permit
     *
     * @return string
     * @throws RuntimeException
     */
    private function getTemplate(IrhpPermitEntity $irhpPermit)
    {
        $irhpPermitTypeId = $irhpPermit->getIrhpPermitApplication()->getIrhpPermitWindow()->getIrhpPermitStock()
            ->getIrhpPermitType()->getId();

        if (!isset($this->templates[$irhpPermitTypeId])) {
            throw new RuntimeException(
                sprintf(
                    'Cover letter template not defined for IRHP Permit Type (id: %s)',
                    $irhpPermitTypeId
                )
            );
        }

        return $this->templates[$irhpPermitTypeId];
    }

    /**
     * Get document query
     *
     * @param IrhpPermitEntity $irhpPermit IRHP Permit
     *
     * @return array
     */
    private function getDocumentQuery(IrhpPermitEntity $irhpPermit)
    {
        $licence = $irhpPermit->getIrhpPermitApplication()->getRelatedApplication()->getLicence();

        $documentQuery = [
            'licence' => $licence->getId(),
            'irhpPermit' => $irhpPermit->getId(),
        ];

        return $documentQuery;
    }
}
