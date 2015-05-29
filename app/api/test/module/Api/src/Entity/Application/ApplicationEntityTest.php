<?php

namespace Dvsa\OlcsTest\Api\Entity\Application;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Mockery as m;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Application Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class ApplicationEntityTest extends EntityTester
{
    public function setUp()
    {
        $this->entity = $this->instantiate($this->entityClass);
    }

    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    /**
     * @group applicationEntity
     */
    public function testGetApplicationDocuments()
    {
        $mockDocument = m::mock()
            ->shouldReceive('getcategory')
            ->andReturn('category')
            ->once()
            ->shouldReceive('getsubCategory')
            ->andReturn('subCategory')
            ->once()
            ->getMock();

        $documentsCollection = new ArrayCollection([$mockDocument]);
        $this->entity->setDocuments($documentsCollection);
        $this->assertEquals($documentsCollection, $this->entity->getApplicationDocuments('category', 'subCategory'));
    }

    /**
     * @dataProvider notValidDataProvider
     * @group applicationEntity
     * @expectedException Dvsa\Olcs\Api\Domain\Exception\ValidationException
     */
    public function testUpdateFinancialHistoryNotValid(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails
    ) {

        $this->entity->updateFinancialHistory(
            $bankrupt,
            $liquidation,
            $receivership,
            $administration,
            $disqualified,
            $insolvencyDetails
        );
    }

    public function notValidDataProvider()
    {
        return [
            ['Y', 'N', 'N', 'N', 'N', '123'],
            ['Y', 'N', 'N', 'N', 'N', ''],
        ];
    }

    /**
     * @dataProvider validDataProvider
     * @group applicationEntity
     */
    public function testUpdateFinancialHistoryValid(
        $bankrupt,
        $liquidation,
        $receivership,
        $administration,
        $disqualified,
        $insolvencyDetails
    ) {

        $this->assertTrue(
            $this->entity->updateFinancialHistory(
                $bankrupt,
                $liquidation,
                $receivership,
                $administration,
                $disqualified,
                $insolvencyDetails
            )
        );
        $this->assertEquals($this->entity->getBankrupt(), $bankrupt);
        $this->assertEquals($this->entity->getLiquidation(), $liquidation);
        $this->assertEquals($this->entity->getReceivership(), $receivership);
        $this->assertEquals($this->entity->getAdministration(), $administration);
        $this->assertEquals($this->entity->getDisqualified(), $disqualified);
        $this->assertEquals($this->entity->getInsolvencyDetails(), $insolvencyDetails);
    }

    public function validDataProvider()
    {
        return [
            ['N', 'N', 'N', 'N', 'N', ''],
            ['Y', 'N', 'N', 'N', 'N', str_repeat('X', 200)],
        ];
    }
}
