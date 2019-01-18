<?php

namespace Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender;

use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Snapshot\Service\Snapshots\AbstractGenerator;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\CurrentDiscsReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DeclarationReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\DocumentationReviewService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\LicenceDetailsService;
use Dvsa\Olcs\Snapshot\Service\Snapshots\Surrender\Section\SignatureReviewService;

class Generator extends AbstractGenerator
{
    public function generate(Surrender $surrender)
    {
        $sections = [
            $this->getLicenceDetailsSection($surrender),
            $this->getCurrentDiscsSection($surrender),
            $this->getDocumentationSection($surrender),
            $this->getDeclarationSection($surrender),
            $this->getSignatureSection($surrender)
        ];

        return $this->generateReadonly(
            [
                'reviewTitle' => 'surrender-review-title',
                'subTitle' => $surrender->getLicence()->getLicNo(),
                'settings' => [
                    'hide-count' => true
                ],
                'sections' => $sections
            ]
        );
    }

    protected function getLicenceDetailsSection(Surrender $licence)
    {
        return [
            'header' => 'surrender-review-licence',
            'config' => $this->getServiceLocator()->get(LicenceDetailsService::class)->getConfigFromData($licence)
        ];
    }

    protected function getCurrentDiscsSection(Surrender $surrender)
    {
        return [
            'header' => 'surrender-review-current-discs',
            'config' => $this->getServiceLocator()->get(CurrentDiscsReviewService::class)->getConfigFromData($surrender)
        ];
    }

    protected function getDocumentationSection(Surrender $surrender)
    {
        return [
            'header' => 'surrender-review-documentation',
            'config' => $this->getServiceLocator()->get(DocumentationReviewService::class)->getConfigFromData($surrender)
        ];
    }

    protected function getDeclarationSection(Surrender $surrender)
    {
        return [
            'header' => 'surrender-review-declaration',
            'config' => $this->getServiceLocator()->get(DeclarationReviewService::class)->getConfigFromData($surrender)
        ];
    }

    protected function getSignatureSection(Surrender $surrender)
    {
        return [
            'config' => $this->getServiceLocator()->get(SignatureReviewService::class)->getConfigFromData($surrender)
        ];
    }
}
