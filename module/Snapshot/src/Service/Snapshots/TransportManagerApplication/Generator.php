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
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\View\Model\ViewModel;

/**
 * Generator
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Generator extends AbstractGenerator
{
    public function generate(TransportManagerApplication $tma)
    {
        $application = $tma->getApplication();
        $licence = $application->getLicence();
        $organisation = $licence->getOrganisation();

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
            'config' => $this->getServiceLocator()->get('Review\TransportManagerMain')->getConfig($tma)
        ];
    }

    protected function getResponsibilityDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-responsibility',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerResponsibility')->getConfig($tma)
        ];
    }

    protected function getOtherEmploymentDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-other-employment',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerOtherEmployment')->getConfig($tma)
        ];
    }

    protected function getPreviousConvictionDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-previous-conviction',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerPreviousConviction')->getConfig($tma)
        ];
    }

    protected function getPreviousLicenceDetailsReviewSection(TransportManagerApplication $tma)
    {
        return [
            'header' => 'tm-review-previous-licence',
            'config' => $this->getServiceLocator()->get('Review\TransportManagerPreviousLicence')->getConfig($tma)
        ];
    }
}
