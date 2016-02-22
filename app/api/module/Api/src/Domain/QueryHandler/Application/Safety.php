<?php

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Safety extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $goodsOrPsv = $application->getGoodsOrPsv()->getId();

        $safetyDocuments = $application->getApplicationDocuments(
            $this->getRepo()->getCategoryReference(Category::CATEGORY_APPLICATION),
            $this->getRepo()->getSubCategoryReference(SubCategory::DOC_SUB_CATEGORY_MAINT_OTHER_DIGITAL)
        );

        return $this->result(
            $application,
            [
                'licence' => [
                    'workshops' => [
                        'contactDetails' => [
                            'address'
                        ]
                    ],
                    'tachographIns'
                ]
            ],
            [
                'safetyDocuments' => $safetyDocuments->toArray(),
                'canHaveTrailers' => ($goodsOrPsv === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE),
                'hasTrailers' => $application->getTotAuthTrailers() > 0
            ]
        );
    }
}
