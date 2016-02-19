<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Transfer\Query\TmResponsibilities\TmResponsibilitiesList as Qry;

/**
 * Transport manager linked applications bookmark
 */
class TmLinkedApplications extends DynamicBookmark
{
    public function getQuery(array $data)
    {
        return Qry::create(['transportManager' => $data['transportManager']]);
    }

    public function render()
    {
        $result = [];

        if (!empty($this->data['tmApplications'])) {
            foreach ($this->data['tmApplications'] as $tmApp) {
                $application = $tmApp->getApplication();

                if (($application instanceof Application) && ($application->getLicence() instanceof Licence)) {
                    $result[] = sprintf(
                        '%s/%d: %s',
                        $application->getLicence()->getLicNo(),
                        $application->getId(),
                        $application->getLicence()->getOrganisation()->getName()
                    );
                }
            }
        }

        return implode("\n", $result);
    }
}
