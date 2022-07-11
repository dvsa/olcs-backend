<?php

/**
 * Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGeneratorServices;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerMainReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerResponsibilityReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerOtherEmploymentReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerPreviousConvictionReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerPreviousLicenceReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerDeclarationReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\TransportManagerSignatureReviewService;
use Dvsa\Olcs\Utils\Translation\NiTextTranslation;
use Laminas\Filter\Word\UnderscoreToCamelCase;
use Laminas\View\Model\ViewModel;

/**
 * Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Generator extends AbstractGenerator
{
    /** @var NiTextTranslation */
    private $niTextTranslation;

    /** @var TransportManagerMainReviewService */
    private $transportManagerMainReviewService;

    /** @var TransportManagerResponsibilityReviewService */
    private $transportManagerResponsibilityReviewService;

    /** @var TransportManagerOtherEmploymentReviewService */
    private $transportManagerOtherEmploymentReviewService;

    /** @var TransportManagerPreviousConvictionReviewService */
    private $transportManagerPreviousConvictionReviewService;

    /** @var TransportManagerPreviousLicenceReviewService */
    private $transportManagerPreviousLicenceReviewService;

    /** @var TransportManagerDeclarationReviewService */
    private $transportManagerDeclarationReviewService;

    /** @var TransportManagerSignatureReviewService */
    private $transportManagerSignatureReviewService;

    /**
     * Create service instance
     *
     * @param AbstractGeneratorServices $abstractGeneratorServices
     * @param NiTextTranslation $niTextTranslation
     * @param TransportManagerMainReviewService $transportManagerMainReviewService
     * @param TransportManagerResponsibilityReviewService $transportManagerResponsibilityReviewService
     * @param TransportManagerOtherEmploymentReviewService $transportManagerOtherEmploymentReviewService
     * @param TransportManagerPreviousConvictionReviewService $transportManagerPreviousConvictionReviewService
     * @param TransportManagerPreviousLicenceReviewService $transportManagerPreviousLicenceReviewService
     * @param TransportManagerDeclarationReviewService $transportManagerDeclarationReviewService
     * @param TransportManagerSignatureReviewService $transportManagerSignatureReviewService
     *
     * @return Generator
     */
    public function __construct(
        AbstractGeneratorServices $abstractGeneratorServices,
        NiTextTranslation $niTextTranslation,
        TransportManagerMainReviewService $transportManagerMainReviewService,
        TransportManagerResponsibilityReviewService $transportManagerResponsibilityReviewService,
        TransportManagerOtherEmploymentReviewService $transportManagerOtherEmploymentReviewService,
        TransportManagerPreviousConvictionReviewService $transportManagerPreviousConvictionReviewService,
        TransportManagerPreviousLicenceReviewService $transportManagerPreviousLicenceReviewService,
        TransportManagerDeclarationReviewService $transportManagerDeclarationReviewService,
        TransportManagerSignatureReviewService $transportManagerSignatureReviewService
    ) {
        parent::__construct($abstractGeneratorServices);
        $this->niTextTranslation = $niTextTranslation;
        $this->transportManagerMainReviewService = $transportManagerMainReviewService;
        $this->transportManagerResponsibilityReviewService = $transportManagerResponsibilityReviewService;
        $this->transportManagerOtherEmploymentReviewService = $transportManagerOtherEmploymentReviewService;
        $this->transportManagerPreviousConvictionReviewService = $transportManagerPreviousConvictionReviewService;
        $this->transportManagerPreviousLicenceReviewService = $transportManagerPreviousLicenceReviewService;
        $this->transportManagerDeclarationReviewService = $transportManagerDeclarationReviewService;
        $this->transportManagerSignatureReviewService = $transportManagerSignatureReviewService;
    }

    public function generate(TransportManagerApplication $tma, $isInternalUser = false)
    {
        $application = $tma->getApplication();
        $licence = $application->getLicence();
        $organisation = $licence->getOrganisation();

        // Set the NI Locale
        $this->niTextTranslation->setLocaleForNiFlag($application->getNiFlag());

        $subTitle = sprintf(
            '%s %s/%s',
            $organisation->getName(),
            $licence->getLicNo(),
            $application->getId()
        );

        $sections = [];

        $sections[] = $this->getMainDetailsReviewSection($tma);
        $sections[] = $this->getResponsibilityDetailsReviewSection($tma);
        $sections[] = $this->getOtherEmploymentDetailsReviewSection($tma);
        $sections[] = $this->getPreviousConvictionDetailsReviewSection($tma);
        $sections[] = $this->getPreviousLicenceDetailsReviewSection($tma);

        // add declaration/signature block if internal user or if TMA status is Op signed
        if ($isInternalUser ||
            $tma->getTmApplicationStatus()->getId() === TransportManagerApplication::STATUS_OPERATOR_SIGNED ||
            $tma->getTmApplicationStatus()->getId() === TransportManagerApplication::STATUS_RECEIVED
        ) {
            $sections[] = $this->getDeclarationReviewSection($tma);
            $sections[] = $this->getSignatureReviewSection($tma);
        }

        return $this->generateReadonly(
            [
                'reviewTitle' => 'tm-review-title',
                'subTitle' => $subTitle,
                'settings' => [
                    'hide-count' => true
                ],
                'sections' => $sections
            ]
        );
    }

    protected function getMainDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-main',
            'config' => $this->transportManagerMainReviewService->getConfig($tma)
        ];
    }

    protected function getResponsibilityDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-responsibility',
            'config' => $this->transportManagerResponsibilityReviewService->getConfig($tma)
        ];
    }

    protected function getOtherEmploymentDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-other-employment',
            'config' => $this->transportManagerOtherEmploymentReviewService->getConfig($tma)
        ];
    }

    protected function getPreviousConvictionDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-previous-conviction',
            'config' => $this->transportManagerPreviousConvictionReviewService->getConfig($tma)
        ];
    }

    protected function getPreviousLicenceDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-previous-licence',
            'config' => $this->transportManagerPreviousLicenceReviewService->getConfig($tma)
        ];
    }

    protected function getDeclarationReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-declaration',
            'config' => $this->transportManagerDeclarationReviewService->getConfig($tma)
        ];
    }

    protected function getSignatureReviewSection(TransportManagerApplication $tma)
    {
        return [
            'config' => $this->transportManagerSignatureReviewService->getConfig($tma)
        ];
    }
}
