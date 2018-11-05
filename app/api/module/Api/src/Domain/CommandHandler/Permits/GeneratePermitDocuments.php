<?php

/**
 * Generate Permit Documents
 */

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Document\GenerateAndStore;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\SubCategory as SubCategoryEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Generate a Permit in RTF form
 *
 * @author Henry White <henry.white@capgemini.com>
 */
final class GeneratePermitDocuments extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'IrhpPermit';

    private $templates = [
        EcmtPermitApplicationEntity::PERMIT_TYPE => [
            'permit' => [
                'templateName' => EcmtPermitApplicationEntity::PERMIT_TEMPLATE_NAME,
                'query' => [
                    'licence' => '',
                    'irhpPermit' => '',
                    'irhpPermitStock' => '',
                    'organisation' => ''
                ],
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT
            ],
            'coveringLetter' => [
                'templateName' => EcmtPermitApplicationEntity::PERMIT_COVERING_LETTER_TEMPLATE_NAME,
                'query' => [
                    'licence' => '',
                    'irhpPermit' => ''
                ],
                'subCategory' => SubCategoryEntity::DOC_SUB_CATEGORY_PERMIT_COVERING_LETTER
            ]
        ]
    ];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $id) {
            $irhpPermit = $this->getRepo()->fetchById($id, Query::HYDRATE_OBJECT);

            $irhpPermitApplication = $irhpPermit->getIrhpPermitApplication();
            $irhpPermitStock = $irhpPermitApplication->getIrhpPermitWindow()->getIrhpPermitStock();
            $irhpPermitType = $irhpPermitStock->getIrhpPermitType()->getName();

            $template = $this->getTemplates()[trim($irhpPermitType)];

            foreach ($template as $documentType => $document) {
                $documentQuery = [];

                foreach ($document['query'] as $key => $queryParam) {
                    $queryParam = '';
                    switch ($key) {
                        case 'licence':
                            $queryParam = $irhpPermitApplication
                                ->getEcmtPermitApplication()
                                ->getLicence()
                                ->getId();
                            break;
                        case 'irhpPermit':
                            $queryParam = $irhpPermit->getId();
                            break;
                        case 'irhpPermitStock':
                            $queryParam = $irhpPermitStock->getId();
                            break;
                        case 'organisation':
                            $queryParam = $irhpPermitApplication
                                ->getEcmtPermitApplication()
                                ->getLicence()
                                ->getOrganisation()
                                ->getId();
                            break;
                    }

                    $documentQuery[$key] = $queryParam;
                }

                $documentDescription = sprintf(
                    '%s %d',
                    strtoupper(str_replace('_', ' ', $document['templateName'])),
                    $irhpPermit->getId()
                );

                $documentGenerated = $this->handleSideEffect(
                    GenerateAndStore::create(
                        [
                            'template' => $document['templateName'],
                            'query' => $documentQuery,
                            'knownValues' => [],
                            'description' => $documentDescription,
                            'category' => CategoryEntity::CATEGORY_PERMITS,
                            'subCategory' => $document['subCategory'],
                            'isExternal' => false,
                            'isScan' => false
                        ]
                    )
                );

                $result->addId($documentType, $documentGenerated->getId('document'), true);
                $result->addMessage($documentDescription . ' RTF created and stored');
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
