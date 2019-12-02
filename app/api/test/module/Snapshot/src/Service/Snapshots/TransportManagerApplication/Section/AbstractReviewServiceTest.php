<?php

namespace Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\OlcsTest\Snapshot\Service\Snapshots\TransportManagerApplication\Section\Stub\AbstractReviewServiceStub;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\I18n\Translator\TranslatorInterface;

/**
 * @covers Dvsa\Olcs\Snapshot\Service\Snapshots\TransportManagerApplication\Section\AbstractReviewService
 */
class AbstractReviewServiceTest extends MockeryTestCase
{
    /** @var AbstractReviewServiceStub */
    private $sut;

    public function setUp()
    {
        $this->sut = m::mock(AbstractReviewServiceStub::class)->makePartial();
    }

    public function testFormatPersonFullName()
    {
        $person = m::mock(Person::class);
        $person->shouldReceive('getTitle->getDescription')
            ->withNoArgs()
            ->andReturn('Mr');
        $person->shouldReceive('getForename')
            ->withNoArgs()
            ->andReturn('John');
        $person->shouldReceive('getFamilyName')
            ->withNoArgs()
            ->andReturn('Smith');

        $this->assertEquals(
            'Mr John Smith',
            $this->sut->formatPersonFullName($person)
        );
    }

    /**
     * @dataProvider dpFormatDate
     */
    public function testFormatDate($expected, $date)
    {
        $this->assertEquals($expected, $this->sut->formatDate($date));
    }

    public function dpFormatDate()
    {
        return [
            ['15 Aug 2005', '2005-08-15T15:52:01+00:00'],
            ['01 Aug 2017', '2017-08-01T15:52:00+05:00'],
        ];
    }

    public function testFormatFullAddress()
    {
        $address = [
            'addressLine1' => 'DVSA',
            'addressLine2' => 'Hillcrest House',
            'addressLine3' => '386 Harehills Lane',
            'addressLine4' => 'Harehills',
            'town' => 'Leeds',
            'postcode' => 'LS9 6NF',
            'countryCode' => [
                'id' => 'UK'
            ]
        ];

        $expected = 'DVSA, Hillcrest House, 386 Harehills Lane, Harehills, Leeds, LS9 6NF, UK';

        $this->assertEquals(
            $expected,
            $this->sut->formatFullAddress($address)
        );
    }

    public function testFormatShortAddress()
    {
        $address = [
            'addressLine1' => 'DVSA',
            'town' => 'Leeds'
        ];

        $expected = 'DVSA, Leeds';

        $this->assertEquals(
            $expected,
            $this->sut->formatShortAddress($address)
        );
    }

    public function testFindFiles()
    {
        $document1 = m::mock(Document::class)->makePartial();
        $document1->setCategory(Category::CATEGORY_LICENSING);
        $document1->setSubCategory(Category::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL);

        $document2 = m::mock(Document::class)->makePartial();
        $document2->setCategory(Category::CATEGORY_LICENSING);
        $document2->setSubCategory(Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE);

        $document3 = m::mock(Document::class)->makePartial();
        $document3->setCategory(Category::CATEGORY_COMPLIANCE);
        $document3->setSubCategory(Category::DOC_SUB_CATEGORY_TRANSPORT_MANAGER_CORRESPONDENCE);

        $document4 = m::mock(Document::class)->makePartial();
        $document4->setCategory(Category::CATEGORY_LICENSING);
        $document4->setSubCategory(Category::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL);

        $document5 = m::mock(Document::class)->makePartial();
        $document5->setCategory(Category::CATEGORY_PERMITS);
        $document5->setSubCategory(Category::BUS_SUB_CATEGORY_OTHER_DOCUMENTS);

        $files = new ArrayCollection(
            [$document1, $document2, $document3, $document4, $document5]
        );

        $foundFiles = $this->sut->findFiles(
            $files,
            Category::CATEGORY_LICENSING,
            Category::DOC_SUB_CATEGORY_APPLICATION_ADVERT_DIGITAL
        );

        $this->assertInstanceOf(ArrayCollection::class, $foundFiles);

        $values = $foundFiles->getValues();

        $this->assertCount(2, $values);
        $this->assertSame($document1, $values[0]);
        $this->assertSame($document4, $values[1]);
    }

    public function testTranslate()
    {
        $translationKey = 'translation.key';
        $translated = 'translated';

        $translator = m::mock(TranslatorInterface::class);

        $this->sut->shouldReceive('getServiceLocator->get')
            ->with('translator')
            ->andReturn($translator);

        $translator->shouldReceive('translate')
            ->with($translationKey, 'snapshot')
            ->andReturn($translated);

        $this->assertEquals(
            $translated,
            $this->sut->translate($translationKey)
        );
    }

    public function testTranslateReplace()
    {
        $translationKey = 'translation.key';
        $translated = 'the %s jumped over the %s hare';
        $arguments = ['fox', 'brown'];

        $expected = 'the fox jumped over the brown hare';

        $this->sut->shouldReceive('translate')
            ->with($translationKey)
            ->andReturn($translated);

        $this->assertEquals(
            $expected,
            $this->sut->translateReplace($translationKey, $arguments)
        );
    }

    /**
     * @dataProvider dpFormatYesNo
     */
    public function testFormatYesNo($value, $expected)
    {
        $this->assertEquals(
            $expected,
            $this->sut->formatYesNo($value)
        );
    }

    public function dpFormatYesNo()
    {
        return [
            ['B','No'],
            ['Z', 'No'],
            ['N', 'No'],
            ['Y', 'Yes'],
        ];
    }
}
