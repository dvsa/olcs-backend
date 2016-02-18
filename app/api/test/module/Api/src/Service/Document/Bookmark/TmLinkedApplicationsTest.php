<?php

namespace Dvsa\OlcsTest\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Api\Service\Document\Bookmark\TmLinkedApplications;

/**
 * Tm Linked Applications test
 */
class TmLinkedApplicationsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $bookmark = new TmLinkedApplications();
        $query = $bookmark->getQuery(['transportManager' => 123]);

        $this->assertInstanceOf(\Dvsa\Olcs\Transfer\Query\QueryInterface::class, $query);
    }

    public function testRender()
    {
        $org = new Organisation();
        $org->setName('Org name');

        $lic1 = new Licence($org, new RefData);
        $lic1->setLicNo('licNo1');

        $app1 = new Application($lic1, new RefData, false);
        $app1->setId(1);

        $tmApp1 = new TransportManagerApplication();
        $tmApp1->setApplication($app1);

        $lic2 = new Licence($org, new RefData);
        $lic2->setLicNo('licNo2');

        $app2 = new Application($lic2, new RefData, false);
        $app2->setId(2);

        $tmApp2 = new TransportManagerApplication();
        $tmApp2->setApplication($app2);

        $bookmark = new TmLinkedApplications();
        $bookmark->setData(
            [
                'tmApplications' => [
                    $tmApp1,
                    $tmApp2
                ]
            ]
        );

        $this->assertEquals(
            "licNo1/1: Org name\nlicNo2/2: Org name",
            $bookmark->render()
        );
    }
}
