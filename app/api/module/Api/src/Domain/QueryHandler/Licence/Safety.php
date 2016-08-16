<?php

/**
 * Safety
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
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
    protected $repoServiceName = 'Licence';

    /**
     * Handle Query
     *
     * @param QueryInterface $query DTO
     *
     * @return \Dvsa\Olcs\Api\Domain\QueryHandler\Result
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchSafetyDetailsUsingId($query);

        $safetyDocuments = $licence->getLicenceDocuments(
            $this->getRepo()->getCategoryReference(Category::CATEGORY_APPLICATION),
            $this->getRepo()->getSubCategoryReference(SubCategory::DOC_SUB_CATEGORY_MAINT_OTHER_DIGITAL)
        );

        $goodsOrPsv = $licence->getGoodsOrPsv()->getId();

        $totalTrailers = $licence->getTotAuthTrailers();
        return $this->result(
            $licence,
            [
                'workshops' => [
                    'contactDetails' => [
                        'address'
                    ]
                ],
            ],
            [
                'safetyDocuments' => $this->resultList($safetyDocuments),
                'canHaveTrailers' => ($goodsOrPsv === LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE),
                'showTrailers' => $totalTrailers > 0 || $totalTrailers === null
            ]
        );
    }
}
