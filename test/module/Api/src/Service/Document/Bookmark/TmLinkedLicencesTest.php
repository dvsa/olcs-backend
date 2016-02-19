<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerLicence;
use Dvsa\Olcs\Api\Service\Document\Bookmark\TmLinkedLicences;

/**
 * Tm Linked Licences test
 */
class TmLinkedLicencesTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new TmLinkedLicences();
        $query = $bookmark->getQuery(['transportManager' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $tm = new TransportManager();
        $org = new Organisation();
        $org->setName('Org name');

        $lic1 = new Licence($org, new RefData);
        $lic1->setLicNo('licNo1');
        $tmLic1 = new TransportManagerLicence($lic1, $tm);

        $lic2 = new Licence($org, new RefData);
        $lic2->setLicNo('licNo2');
        $tmLic2 = new TransportManagerLicence($lic2, $tm);

        $bookmark = new TmLinkedLicences();
        $bookmark->setData(
            [
                'result' => [
                    $tmLic1,
                    $tmLic2
                ]
            ]
        );

        $this->assertEquals(
            "licNo1: Org name\nlicNo2: Org name",
            $bookmark->render()
        );
    }
}
