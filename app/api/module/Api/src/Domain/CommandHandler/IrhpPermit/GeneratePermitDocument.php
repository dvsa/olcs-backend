<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country as CountryEntity;
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
 * Generate Permit Document for IRHP Permit
 */
final class GeneratePermitDocument extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpPermit';

    /**
     * @var array
     */
    private $templates = [
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT
            => EcmtPermitApplicationEntity::PERMIT_TEMPLATE_NAME,
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM
            => DocumentEntity::IRHP_PERMIT_SHORT_TERM_ECMT,
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL
            => DocumentEntity::IRHP_PERMIT_ECMT_REMOVAL,
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL => [
            CountryEntity::ID_AUSTRIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_AUSTRIA,
            CountryEntity::ID_BELGIUM => DocumentEntity::IRHP_PERMIT_ANN_BILAT_BELGIUM,
            CountryEntity::ID_BULGARIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_BULGARIA,
            CountryEntity::ID_CROATIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CROATIA,
            CountryEntity::ID_CYPRUS => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CYPRUS,
            CountryEntity::ID_CZECH_REPUBLIC => DocumentEntity::IRHP_PERMIT_ANN_BILAT_CZECH_REPUBLIC,
            CountryEntity::ID_DENMARK => DocumentEntity::IRHP_PERMIT_ANN_BILAT_DENMARK,
            CountryEntity::ID_ESTONIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ESTONIA,
            CountryEntity::ID_FINLAND => DocumentEntity::IRHP_PERMIT_ANN_BILAT_FINLAND,
            CountryEntity::ID_FRANCE => DocumentEntity::IRHP_PERMIT_ANN_BILAT_FRANCE,
            CountryEntity::ID_GERMANY => DocumentEntity::IRHP_PERMIT_ANN_BILAT_GERMANY,
            CountryEntity::ID_GREECE => DocumentEntity::IRHP_PERMIT_ANN_BILAT_GREECE,
            CountryEntity::ID_HUNGARY => DocumentEntity::IRHP_PERMIT_ANN_BILAT_HUNGARY,
            CountryEntity::ID_ICELAND => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ICELAND,
            CountryEntity::ID_IRELAND => DocumentEntity::IRHP_PERMIT_ANN_BILAT_IRELAND,
            CountryEntity::ID_ITALY => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ITALY,
            CountryEntity::ID_LATVIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LATVIA,
            CountryEntity::ID_LIECHTENSTEIN => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LIECHTENSTEIN,
            CountryEntity::ID_LITHUANIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LITHUANIA,
            CountryEntity::ID_LUXEMBOURG => DocumentEntity::IRHP_PERMIT_ANN_BILAT_LUXEMBOURG,
            CountryEntity::ID_MALTA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_MALTA,
            CountryEntity::ID_NETHERLANDS => DocumentEntity::IRHP_PERMIT_ANN_BILAT_NETHERLANDS,
            CountryEntity::ID_NORWAY => DocumentEntity::IRHP_PERMIT_ANN_BILAT_NORWAY,
            CountryEntity::ID_POLAND => DocumentEntity::IRHP_PERMIT_ANN_BILAT_POLAND,
            CountryEntity::ID_PORTUGAL => DocumentEntity::IRHP_PERMIT_ANN_BILAT_PORTUGAL,
            CountryEntity::ID_ROMANIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_ROMANIA,
            CountryEntity::ID_SLOVAKIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SLOVAKIA,
            CountryEntity::ID_SLOVENIA => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SLOVENIA,
            CountryEntity::ID_SPAIN => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SPAIN,
            CountryEntity::ID_SWEDEN => DocumentEntity::IRHP_PERMIT_ANN_BILAT_SWEDEN,
        ],
        IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL
            => DocumentEntity::IRHP_PERMIT_ANN_MULTILAT,
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
            $irhpPermit->getPermitNumber()
        );

        $document = $this->handleSideEffect(
            GenerateAndStore::create(
                [
                    'template' => $template,
                    'query' => $this->getDocumentQuery($irhpPermit),
                    'knownValues' => [],
                    'description' => $description,
                    'category' => CategoryEntity::CATEGORY_PERMITS,
                    'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT,
                    'isExternal' => false,
                    'isScan' => false
                ]
            )
        );

        $this->result->addId('permit', $document->getId('document'), true);
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
        $irhpPermitStock = $irhpPermit->getIrhpPermitApplication()->getIrhpPermitWindow()->getIrhpPermitStock();
        $irhpPermitTypeId = $irhpPermitStock->getIrhpPermitType()->getId();

        switch ($irhpPermitTypeId) {
            case IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL:
                // those templates are country specific
                $countryId = $irhpPermitStock->getCountry()->getId();
                $template = isset($this->templates[$irhpPermitTypeId][$countryId])
                    ? $this->templates[$irhpPermitTypeId][$countryId] : null;
                break;
            default:
                $template = isset($this->templates[$irhpPermitTypeId]) ? $this->templates[$irhpPermitTypeId] : null;
                break;
        }

        if (!isset($template)) {
            throw new RuntimeException(
                sprintf(
                    'Permit template not defined for IRHP Permit Type (id: %s)',
                    $irhpPermitTypeId
                )
            );
        }

        return $template;
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
        $irhpPermitApplication = $irhpPermit->getIrhpPermitApplication();
        $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
        $licence = $irhpPermitApplication->getIrhpApplication()->getLicence();

        $documentQuery = [
            'licence' => $licence->getId(),
            'irhpPermit' => $irhpPermit->getId(),
            'irhpPermitStock' => $irhpPermitStock->getId(),
            'organisation' => $licence->getOrganisation()->getId(),
        ];

        return $documentQuery;
    }
}
