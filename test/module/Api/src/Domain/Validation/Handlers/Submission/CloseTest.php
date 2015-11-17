<?php

/**
 * Close Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Validation\Handlers\Submission;

use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\OlcsTest\Api\Domain\Validation\Handlers\AbstractHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\Submission\Close;
use Zend\ServiceManager\ServiceManager;
use Dvsa\Olcs\Transfer\Command\Submission\CloseSubmission as Cmd;

/**
 * Close Test
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class CloseTest extends AbstractHandlerTestCase
{
    /**
     * @var Close
     */
    protected $sut;

    /**
     * @var Integer
     */
    protected $dtoId;

    public function setUp()
    {
        $this->sut = new Close();
        $this->dtoId = 99;

        parent::setUp();
    }

    public function testIsValidInternal()
    {
        $caseId = 85;
        $dto = Cmd::create(['id' => $this->dtoId, 'case' => $caseId]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);
        $this->setIsValid('canCloseSubmission', [$this->dtoId], true);
        $this->setIsValid('submissionBelongsToCase', [$this->dtoId, $caseId], true);

        $this->assertTrue($this->sut->isValid($dto));
    }

    public function testIsNotInternal()
    {
        $caseId = 85;
        $dto = Cmd::create(['id' => $this->dtoId, 'case' => $caseId]);

        $this->setIsGranted(Permission::INTERNAL_USER, false);
        $this->setIsValid('canCloseSubmission', [$this->dtoId], true);
        $this->setIsValid('submissionBelongsToCase', [$this->dtoId, $caseId], true);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testCannotCloseSubmission()
    {
        $caseId = 85;
        $dto = Cmd::create(['id' => $this->dtoId, 'case' => $caseId]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);
        $this->setIsValid('canCloseSubmission', [$this->dtoId], false);
        $this->setIsValid('submissionBelongsToCase', [$this->dtoId, $caseId], true);

        $this->assertFalse($this->sut->isValid($dto));
    }

    public function testInvalidCaseForSubmission()
    {
        $caseId = 85;
        $dto = Cmd::create(['id' => $this->dtoId, 'case' => $caseId]);

        $this->setIsGranted(Permission::INTERNAL_USER, true);
        $this->setIsValid('canCloseSubmission', [$this->dtoId], true);
        $this->setIsValid('submissionBelongsToCase', [$this->dtoId, $caseId], false);

        $this->assertFalse($this->sut->isValid($dto));
    }
}
