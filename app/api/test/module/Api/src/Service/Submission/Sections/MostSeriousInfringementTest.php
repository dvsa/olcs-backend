<?php

namespace Dvsa\OlcsTest\Api\Service\Submission\Sections;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement;
use Dvsa\Olcs\Api\Entity\Si\SiCategoryType;
use Mockery as m;

/**
 * Class MostSeriousInfringementTest
 * @author Shaun Lizzio <shaun@valtech.co.uk>
 */
class MostSeriousInfringementTest extends SubmissionSectionTest
{
    protected $submissionSection = '\Dvsa\Olcs\Api\Service\Submission\Sections\MostSeriousInfringement';

    protected $expectedResult = [
        'id' => 66,
        'notificationNumber' => 'not no 123',
        'siCategory' => 'si_cat-desc',
        'siCategoryType' => 'si_cat_type-desc',
        'infringementDate' => '2014-05-05',
        'checkDate' => '2014-01-01',
        'isMemberState' => true
    ];

    /**
     * Filter provider
     *
     * @return array
     */
    public function sectionTestProvider()
    {
        $case = $this->getCase();

        $expectedResult = ['data' => ['overview' => $this->expectedResult]];

        return [
            [$case, $expectedResult],
        ];
    }

    protected function getCase()
    {
        $case = parent::getCase();

        $seriousInfringements = new ArrayCollection();

        $si = m::mock(SeriousInfringement::class)->makePartial();
        $si->setId(66);
        $si->setNotificationNumber('not no 123');
        $si->setCheckDate('2014-01-01');
        $si->setSiCategory($this->generateRefDataEntity('si_cat'));
        $si->setInfringementDate('2014-05-05');

        $siCategoryType = new SiCategoryType();
        $siCategoryType->setDescription('si_cat_type-desc');
        $si->setSiCategoryType($siCategoryType);

        $country = new Country();
        $country->setIsMemberState(true);
        $si->setMemberStateCode($country);

        $seriousInfringements->add($si);

        $case->setSeriousInfringements($seriousInfringements);

        return $case;
    }
}
