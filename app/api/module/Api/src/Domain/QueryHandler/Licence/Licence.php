<?php

/**
 * Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Licence extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Licence';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionConfig
     */
    private $sectionConfig;

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\SectionAccessService
     */
    private $sectionAccessService;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->sectionConfig = $mainServiceLocator->get('SectionConfig');
        $this->sectionAccessService = $mainServiceLocator->get('SectionAccessService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        $licence = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licence,
            [],
            [
                'sections' => $this->getSections($licence)
            ]
        );
    }

    protected function getSections(LicenceEntity $licence)
    {
        $location = $this->isGranted(Permission::INTERNAL_USER) ? 'internal' : 'external';

        $hasConditions = $licence->hasApprovedUnfulfilledConditions();

        $goodsOrPsv = null;
        if ($licence->getGoodsOrPsv() !== null) {
            $goodsOrPsv = $licence->getGoodsOrPsv()->getId();
        }

        $licenceType = null;
        if ($licence->getLicenceType() !== null) {
            $licenceType = $licence->getLicenceType()->getId();
        }

        $access = [
            $location,
            'licence',
            $goodsOrPsv,
            $licenceType,
            $hasConditions ? 'hasConditions' : 'noConditions'
        ];

        $inputSections = $this->sectionConfig->getAll();

        return $this->sectionAccessService->setSections($inputSections)->getAccessibleSections($access);
    }
}
