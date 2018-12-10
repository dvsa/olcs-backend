<?php

/**
 * Naming Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\System\SubCategory;
use Dvsa\Olcs\Api\Service\Document\ContextProviderInterface;
use Mockery as m;
use Dvsa\Olcs\Api\Service\Document\NamingService;
use Dvsa\Olcs\DocumentShare\Data\Object\File;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use OlcsTest\Bootstrap;

/**
 * Naming Service Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class NamingServiceTest extends MockeryTestCase
{
    /**
     * @var NamingService
     */
    protected $sut;

    public function setUp()
    {
        $config = [
            'document_share' => [
                'path' => 'documents/'
                    . '{Category}/{SubCategory}/{Date:Y}/{Date:m}/{Date:YmdHis}_{Context}_{Description}.{Extension}'
            ]
        ];

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Config', $config);

        $this->sut = new NamingService();
        $this->sut->createService($sm);
    }

    public function testCreateServiceFail()
    {
        $this->expectException('\RuntimeException');

        $config = [];

        $sm = Bootstrap::getServiceManager();
        $sm->setService('Config', $config);

        $this->sut = new NamingService();
        $this->sut->createService($sm);
    }

    public function testGenerateName()
    {
        $date = new DateTime();

        /** @var Category $category */
        $category = m::mock(Category::class)->makePartial();
        $category->setDescription('Cat');

        /** @var SubCategory $subCategory */
        $subCategory = m::mock(SubCategory::class)->makePartial();
        $subCategory->setSubCategoryName('Sub Cat');

        $name = $this->sut->generateName('Some Desc', 'rtf', $category, $subCategory);

        $expected = sprintf(
            'documents/Cat/Sub_Cat/%s/%s/%s__Some_Desc.rtf',
            $date->format('Y'),
            $date->format('m'),
            $date->format('YmdHis')
        );

        $this->assertEquals($expected, $name);
    }

    public function testGenerateNameWithUnknown()
    {
        $date = new DateTime();

        $name = $this->sut->generateName('Some :/] Desc', 'rtf');

        $expected = sprintf(
            'documents/Unknown/Unknown/%s/%s/%s__Some___Desc.rtf',
            $date->format('Y'),
            $date->format('m'),
            $date->format('YmdHis')
        );

        $this->assertEquals($expected, $name);
    }

    public function testGenerateNameWithEntity()
    {
        $date = new DateTime();

        /** @var Category $category */
        $category = m::mock(Category::class)->makePartial();
        $category->setDescription('Cat');

        /** @var SubCategory $subCategory */
        $subCategory = m::mock(SubCategory::class)->makePartial();
        $subCategory->setSubCategoryName('Sub Cat');

        $entity = m::mock(ContextProviderInterface::class);
        $entity->shouldReceive('getContextValue')
            ->andReturn('12345');

        $name = $this->sut->generateName('[Some :Desc\/]', 'rtf', $category, $subCategory, $entity);

        $expected = sprintf(
            'documents/Cat/Sub_Cat/%s/%s/%s_12345_Some_Desc_.rtf',
            $date->format('Y'),
            $date->format('m'),
            $date->format('YmdHis')
        );

        $this->assertEquals($expected, $name);
    }
}
