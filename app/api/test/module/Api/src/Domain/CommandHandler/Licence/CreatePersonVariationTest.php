<?php
/**
 * Created by PhpStorm.
 * User: shaunhare
 * Date: 23/10/2017
 * Time: 15:52
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Licence;


use Dvsa\Olcs\Api\Domain\CommandHandler\Licence\CreatePersonVariation;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;

class CreatePersonVariationTest extends CommandHandlerTestCase
{

    /**
     * @var CreatePersonVariation
     */
    private $sut;

    public function setUp()
    {
        $this->sut = new CreatePersonVariation();

        parent::setUp();
    }


}
