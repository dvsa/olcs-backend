<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Exception;

use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Note\Note as NoteEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Create Update Abstract a Note
 */
abstract class CreateUpdateAbstract extends AbstractCommandHandler
{
    protected $repoServiceName = 'Note';

    /**
     * This user ID is hard coded. Change it.
     *
     * @var int
     * @deprecated REMOVE THIS AND DO IT PROPERLY ASAP!
     */
    private $hardCodedUserId = '1';

    /**
     * @param CommandInterface $command
     * @return Entity\Note\Note
     */
    public function getNoteEntity(CommandInterface $command)
    {
        $entity = $this->retrieveEntity($command);

        if ($command->getApplication() !== null) {

            $application = $this->getRepo()->getReference(
                Entity\Application\Application::class,
                $command->getApplication()
            );

            $entity->setApplication($application);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, NoteEntity::NOTE_TYPE_APPLICATION));
        }

        if ($command->getBusReg() !== null) {

            $busReg = $this->getRepo()->getReference(
                Entity\Bus\BusReg::class,
                $command->getBusReg()
            );

            $entity->getBusReg($busReg);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, NoteEntity::NOTE_TYPE_BUS));
        }

        if ($command->getCase() !== null) {

            $case = $this->getRepo()->getReference(
                Entity\Cases\Cases::class,
                $command->getCase()
            );

            $entity->setCase($case);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, NoteEntity::NOTE_TYPE_CASE));
        }

        if ($command->getLicence() !== null) {

            $licence = $this->getRepo()->getReference(
                Entity\Licence\Licence::class,
                $command->getLicence()
            );

            $entity->setLicence($licence);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, NoteEntity::NOTE_TYPE_LICENCE));
        }

        if ($command->getOrganisation() !== null) {

            $org = $this->getRepo()->getReference(
                Entity\Licence\Licence::class,
                $command->getOrganisation()
            );

            $entity->setOrganisation($org);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, NoteEntity::NOTE_TYPE_ORGANISATION));
        }

        if ($command->getTransportManager() !== null) {

            $transportManager = $this->getRepo()->getReference(
                Entity\TM\TransportManager::class,
                $command->getTransportManager()
            );

            $entity->setTransportManager($transportManager);
            $entity->setNoteType(
                $this->getRepo()->getReference(
                    RefData::class, NoteEntity::NOTE_TYPE_TRANSPORT_MANAGER
                )
            );
        }

        $entity->setUser(
            $this->getRepo()->getReference(Entity\User\User::class,
                $this->hardCodedUserId)
        );

        return $entity;
    }

    /**
     * @param CommandInterface $command
     * @return Entity\Note\Note
     */
    abstract protected function retrieveEntity(CommandInterface $command);
}
