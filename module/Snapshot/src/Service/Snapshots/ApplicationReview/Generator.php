<?php

/**
 * Application Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Snapshot\Service\Snapshots\ApplicationReview;

use Zend\Filter\Word\UnderscoreToCamelCase;

/**
 * Application Review
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Generator
{
    protected $lva;

    public function generate($data)
    {
        if ($data['isVariation']) {
            $this->lva = 'variation';
        } else {
            $this->lva = 'application';
        }

        $config = $this->buildReadonlyConfigForSections($data['sections'], $data);

        // Generate readonly markup
        return $this->generateReadonly($config);
    }

    protected function generateReadonly(array $config)
    {
        return '';
    }

    protected function buildReadonlyConfigForSections($sections, $reviewData)
    {
        $entity = ucfirst($this->lva);

        $filter = new UnderscoreToCamelCase();

        $sectionConfig = [];

        foreach ($sections as $section) {
            $serviceName = 'Review\\' . $entity . ucfirst($filter->filter($section));
            $config = null;

            // @NOTE this check is in place while we implement each section
            // eventually we should be able to remove the if
            if ($this->getServiceLocator()->has($serviceName)) {
                $service = $this->getServiceLocator()->get($serviceName);
                $config = $service->getConfigFromData($reviewData);
            }

            $sectionConfig[] = [
                'header' => 'review-' . $section,
                'config' => $config
            ];
        }

        return [
            'reviewTitle' => $this->getTitle($reviewData),
            'subTitle' => $this->getSubTitle($reviewData),
            'sections' => $sectionConfig
        ];
    }

    protected function getSubTitle($data)
    {
        return sprintf('%s %s/%s', $data['licence']['organisation']['name'], $data['licence']['licNo'], $data['id']);
    }

    protected function getTitle($data)
    {
        return sprintf(
            '%s-review-title-%s%s',
            $this->lva,
            $data['isGoods'] ? 'gv' : 'psv',
            $this->isNewPsvSpecialRestricted($data) ? '-sr' : ''
        );
    }

    protected function isNewPsvSpecialRestricted($data)
    {
        return $this->lva === 'application' && !$data['isGoods'] && $data['isSpecialRestricted'];
    }
}
