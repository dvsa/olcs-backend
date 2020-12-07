<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Service\OpenAm;
use Dvsa\Olcs\Cli\Domain\Command\ImportUsersFromCsv as Cmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\ImportUsersFromCsv;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use org\bovigo\vfs;
use org\bovigo\vfs\vfsStream;

/**
 * @covers \Dvsa\Olcs\Cli\Domain\CommandHandler\ImportUsersFromCsv
 */
class ImportUsersFromCsvTest extends CommandHandlerTestCase
{
    const PASSWORD = 'unit_pass';
    const PID = 'unit_pid';

    /** @var ImportUsersFromCsv */
    protected $sut;

    /** @var  array */
    private $lineParts = [
        'type' => 'internal',
        'team' => 'correct_team',
        'role' => Entity\User\Role::ROLE_INTERNAL_ADMIN,
        'fn' => 'fn',
        'sn' => 'sn',
        'email' => 'email@domain.com',
    ];

    /** @var  vfs\vfsStreamDirectory */
    private $vfs;
    /** @var  vfs\vfsStreamFile */
    private $srcFile;
    /** @var  string */
    private $srcPath;
    /** @var  vfs\vfsStreamFile */
    private $resFile;
    /** @var  string */
    private $resPath;

    /** @var  m\MockInterface */
    private $mockOpenAmCli;
    /** @var  m\MockInterface */
    private $mockOpenAmSrv;

    /** @var m\MockInterface */
    private $mockUserRepo;
    /** @var m\MockInterface | Entity\User\Role $mockRole  */
    private $mockRole;
    /** @var m\MockInterface */
    private $mockRoleRepo;
    /** @var m\MockInterface */
    private $mockTeam;
    /** @var m\MockInterface */
    private $mockTeamRepo;

    public function setUp(): void
    {
        $this->sut = new ImportUsersFromCsv;

        //  mock repos
        $this->mockUserRepo = $this->mockRepo('User', Repository\User::class);
        $this->mockUserRepo->shouldReceive('getRefdataReference')->andReturn(new Entity\System\RefData());

        $this->mockRole = m::mock(Entity\User\Role::class)->makePartial();
        $this->mockRole->setRole(Entity\User\Role::ROLE_INTERNAL_ADMIN);

        $this->mockRoleRepo = $this->mockRepo('Role', Repository\Role::class);
        $this->mockRoleRepo
            ->shouldReceive('fetchByRole')
            ->with(Entity\User\Role::ROLE_INTERNAL_ADMIN)
            ->andReturn($this->mockRole);

        /** @var m\MockInterface | Entity\User\Team $mockRole */
        $this->mockTeam = m::mock(Entity\User\Team::class)->makePartial();

        $this->mockTeamRepo = $this->mockRepo('Team', Repository\Team::class);
        $this->mockTeamRepo->shouldReceive('fetchByName')->with('correct_team')->andReturn([$this->mockTeam]);

        //  mock sl
        $this->mockOpenAmSrv = m::mock(OpenAm\UserInterface::class);
        $this->mockOpenAmSrv
            ->shouldReceive('generatePassword')->andReturn(self::PASSWORD)
            ->shouldReceive('generatePid')->andReturn(self::PID);

        $this->mockOpenAmCli = m::mock(OpenAm\ClientInterface::class);

        $this->mockedSmServices += [
            OpenAm\ClientInterface::class => $this->mockOpenAmCli,
            OpenAm\UserInterface::class => $this->mockOpenAmSrv,
        ];

        parent::setUp();

        $this->vfs = vfs\vfsStream::setup('unit');

        $this->srcFile = vfsStream::newFile('source.csv');
        $this->srcFile->setContent(
            'Titles' . PHP_EOL .
            implode(",", $this->lineParts) . PHP_EOL
        );
        $this->vfs->addChild($this->srcFile);
        $this->srcPath = $this->srcFile->url();

        $this->resFile = vfsStream::newFile('source_result.csv');
        $this->vfs->addChild($this->resFile);
        $this->resPath = $this->resFile->url();

        $this->vfs->addChild($this->srcFile);
    }

    protected function initReferences()
    {
        $this->refData = [
            Entity\ContactDetails\ContactDetails::CONTACT_TYPE_USER,
        ];

        parent::initReferences();
    }

    public function testInvalidPathException()
    {
        $path = 'unit_wrong_path';
        $this->expectException(\Exception::class, sprintf(ImportUsersFromCsv::ERR_FILE_404, $path));

        $cmd = Cmd::create(
            [
                'csvPath' => $path,
            ]
        );
        $this->sut->handleCommand($cmd);
    }

    public function testResultCsvFileCanOpenExc()
    {
        $resFile = vfsStream::newFile('custom_result.csv');
        $this->vfs->addChild($this->resFile);
        $resPath = $resFile->url();

        $resFile->chmod(0);

        $this->expectException(\Exception::class, sprintf(ImportUsersFromCsv::ERR_RES_FILE_CANT_OPEN, $resPath));

        $cmd = Cmd::create(
            [
                'csvPath' => $this->srcPath,
                'resultCsvPath' => $resPath,
            ]
        );
        $this->sut->handleCommand($cmd);
    }

    public function testCsvFileCanOpenExc()
    {
        $this->srcFile->chmod(0);

        $this->expectException(
            \Exception::class,
            sprintf(ImportUsersFromCsv::ERR_FILE_CANT_OPEN, $this->srcPath)
        );

        $cmd = Cmd::create(
            [
                'csvPath' => $this->srcPath,
            ]
        );
        $this->sut->handleCommand($cmd);
    }

    public function testProcessInvalidCntFields()
    {
        array_shift($this->lineParts);
        $this->srcFile->setContent(
            'Titles' . PHP_EOL .
            implode(",", $this->lineParts) . PHP_EOL
        );

        $resFile = vfsStream::newFile('custom_result.csv');
        $this->vfs->addChild($resFile);
        $resPath = $resFile->url();

        //  prepare expected csv content
        $lines  =[
            ['Titles', 'loginId', 'password', 'status'],
            $this->lineParts + ['status' => ImportUsersFromCsv::CSV_ERR_INVALID_ROW],

        ];
        $expect = $this->getCsv($lines);

        //  call & check
        $this->sut->handleCommand(
            Cmd::create(
                [
                    'csvPath' => $this->srcPath,
                    'resultCsvPath' => $resPath,
                ]
            )
        );

        static::assertEquals($expect, file_get_contents($resPath));
    }

    /**
     * @dataProvider dpTestProcessCheckValidators
     */
    public function testProcessCheckValidators($lineParts, $expectParts)
    {
        $lineParts = array_merge($this->lineParts, $lineParts);
        $this->srcFile->setContent(
            'Titles' . PHP_EOL .
            implode(",", $lineParts) . PHP_EOL
        );

        $this->mockRoleRepo->shouldReceive('fetchByRole')->with('wrong_role')->andReturnNull();
        $this->mockTeamRepo->shouldReceive('fetchByName')->with('wrong_team')->andReturn([]);
        $this->mockUserRepo->shouldReceive('findUserNameAvailable')->with('fn.sn')->andReturnNull();

        //  prepare expected csv content
        $expect = $this->getExpect($lineParts, $expectParts);

        //  call & check
        $this->sut->handleCommand(
            Cmd::create(['csvPath' => $this->srcPath])
        );

        static::assertEquals($expect, file_get_contents($this->resPath));
    }

    public function dpTestProcessCheckValidators()
    {
        return [
            'wrong_type' => [
                'lineParts' => [
                    'type' => 'wrong_type',
                ],
                'expect' => [
                    'type' => 'wrong_type',
                    'status' => sprintf(
                        ImportUsersFromCsv::CSV_ERR_USER_TYPE_INVALID,
                        'wrong_type',
                        Entity\User\User::USER_TYPE_INTERNAL
                    ),
                ],
            ],
            'wrong_email' => [
                'lineParts' => [
                    'email' => 'wrong@email@com',
                ],
                'expect' => [
                    'email' => 'wrong@email@com',
                    'status' => sprintf(ImportUsersFromCsv::CSV_ERR_EMAIL_INVALID, 'wrong@email@com'),
                ],
            ],
            'wrong_role' => [
                'lineParts' => [
                    'role' => 'wrong_role',
                ],
                'expect' => [
                    'role' => 'wrong_role',
                    'status' => sprintf(ImportUsersFromCsv::CSV_ERR_USER_ROLE_INVALID, 'wrong_role'),
                ],
            ],
            'wrong_team' => [
                'lineParts' => [
                    'team' => 'wrong_team',
                ],
                'expect' => [
                    'team' => 'wrong_team',
                    'status' => sprintf(ImportUsersFromCsv::CSV_ERR_USER_TEAM_INVALID, 'wrong_team'),
                ],
            ],
            'busy_usernane' => [
                'lineParts' => [],
                'expect' => [
                    'status' => sprintf(
                        ImportUsersFromCsv::CSV_ERR_USER_ALREADY_IN_DB,
                        Repository\User::USERNAME_GEN_TRY_COUNT
                    ),
                ],
            ],
        ];
    }

    public function testProcessDbSaveFailed()
    {
        $this->mockUserRepo->shouldReceive('findUserNameAvailable')->with('fn.sn')->andReturn('fn.sn');

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')->once()
            ->shouldReceive('rollback')->once();

        $excMsg = 'Save Error Message';
        $this->mockUserRepo->shouldReceive('save')->once()->andThrow(\Exception::class, $excMsg);

        //  prepare expected csv content
        $expect = $this->getExpect(
            $this->lineParts,
            [
                'status' => sprintf(ImportUsersFromCsv::CSV_ERR_USER_NOT_CREATED_IN_DB, $excMsg),
            ]
        );

        //  call & check
        $this->sut->handleCommand(
            Cmd::create(['csvPath' => $this->srcPath])
        );

        static::assertEquals($expect, file_get_contents($this->resPath));
    }

    public function testProcessOpenAmSaveFailed()
    {
        $this->mockUserRepo
            ->shouldReceive('findUserNameAvailable')->with('fn.sn')->andReturn('fn.sn');

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')->once()
            ->shouldReceive('rollback')->once();

        $this->mockUserRepo->shouldReceive('save')->once();

        $excMsg = 'OpenAM Save Error Message ' . "\nX\rY";

        /** @var \Laminas\Http\Response $mockResp */
        $mockResp = m::mock(\Laminas\Http\Response::class)
            ->shouldReceive('getContent')->andReturn($excMsg)
            ->getMock();
        $this->mockOpenAmCli
            ->shouldReceive('registerUser')
            ->once()
            ->andThrow(new OpenAm\FailedRequestException($mockResp));

        //  prepare expected csv content
        $expectParts = [
            'status' => sprintf(
                ImportUsersFromCsv::CSV_ERR_USER_NOT_CREATED_IN_OPENAM,
                'Invalid response from OpenAm service: OpenAM Save Error Message XY'
            ),
        ];
        $expect = $this->getExpect($this->lineParts, $expectParts);

        //  call & check
        $this->sut->handleCommand(
            Cmd::create(['csvPath' => $this->srcPath])
        );

        static::assertEquals($expect, file_get_contents($this->resPath));
    }

    public function testSuccess()
    {
        $login = 'unit_login';

        $this->mockUserRepo->shouldReceive('findUserNameAvailable')->with('fn.sn')->andReturn($login);

        $this->mockTransationMngr
            ->shouldReceive('beginTransaction')->once()
            ->shouldReceive('commit')->once();

        $this->mockUserRepo
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (Entity\User\User $user) {
                    static::assertEquals('unit_login', $user->getLoginId());
                    static::assertEquals($this->mockRole, $user->getRoles()->current());
                    static::assertEquals($this->mockTeam, $user->getTeam());

                    $cd = $user->getContactDetails();
                    static::assertEquals($this->lineParts['email'], $cd->getEmailAddress());

                    $person = $cd->getPerson();
                    static::assertEquals($this->lineParts['fn'], $person->getForename());
                    static::assertEquals($this->lineParts['sn'], $person->getFamilyName());
                }
            );

        $this->mockOpenAmCli
            ->shouldReceive('registerUser')
            ->once()
            ->with(
                $login,
                self::PID,
                $this->lineParts['email'],
                $this->lineParts['sn'],
                $this->lineParts['fn'],
                OpenAm\Client::REALM_INTERNAL,
                self::PASSWORD
            );

        //  prepare expected csv content
        $expect = $this->getExpect($this->lineParts, ['login' => 'unit_login']);

        //  call & check
        $this->sut->handleCommand(
            Cmd::create(['csvPath' => $this->srcPath])
        );

        static::assertEquals($expect, file_get_contents($this->resPath));
    }

    private function getExpect($lineParts, array $expectParts)
    {
        $expectParts = array_filter(
            array_merge(
                [
                    'login' => 'fn.sn',
                    'pass' => self::PASSWORD,
                    'status' => 'OK',
                ],
                $expectParts
            )
        );

        $lines  =[
            ['Titles', 'loginId', 'password', 'status'],
            $lineParts + $expectParts
        ];

        return $this->getCsv($lines);
    }

    private function getCsv(array $lines)
    {
        $fhTmpCsv = fopen('php://memory', 'wb+');

        foreach ($lines as $line) {
            fputcsv($fhTmpCsv, $line);
        }

        rewind($fhTmpCsv);
        $expect = stream_get_contents($fhTmpCsv);

        fclose($fhTmpCsv);

        return $expect;
    }
}
