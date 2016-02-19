<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Transfer\Query\TmResponsibilities\TmResponsibilitiesList as Qry;

/**
 * Transport manager linked licences bookmark
 */
class TmLinkedLicences extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return Qry::create(['transportManager' => $data['transportManager']]);
    }

    public function render()
    {
        $result = [];

        if (!empty($this->data['result'])) {
            foreach ($this->data['result'] as $tmLic) {
                $licence = $tmLic->getLicence();

                if ($licence instanceof Licence) {
                    $result[] = sprintf(
                        '%s: %s',
                        $licence->getLicNo(),
                        $licence->getOrganisation()->getName()
                    );
                }
            }
        }

        return implode("\n", $result);
    }
}
