<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Processing\Note;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepository;
use Dvsa\Olcs\Transfer\Command\Processing\Note\Create as CreateCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Exception;

use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Create a Note
 */
final class Create extends AbstractCommandHandler
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
     * @param CreateCommand $command
     * @throws Exception
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var NoteRepository $repo */
        $repo = $this->getRepo();

        try {

            $repo->beginTransaction();

            $note = $this->getNoteEntity($command);
            $note->setComment($command->getComment());

            $this->getRepo()->save($note);
            $this->getRepo()->commit();

            $result->addId('note', $note->getId());
            $result->addMessage('Note created');

            return $result;

        } catch (\Exception $ex) {

            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    /**
     * @param CreateCommand $command
     * @return Entity\Note\Note
     */
    public function getNoteEntity(CreateCommand $command)
    {
        $entity = new Entity\Note\Note();

        if ($command->getApplication() !== null) {

            $application = $this->getRepo()->getReference(
                Entity\Application\Application::class,
                $command->getApplication()
            );

            $entity->setApplication($application);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_app'));
        }

        if ($command->getBusReg() !== null) {

            $busReg = $this->getRepo()->getReference(
                Entity\Bus\BusReg::class,
                $command->getBusReg()
            );

            $entity->getBusReg($busReg);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_bus'));
        }

        if ($command->getCase() !== null) {

            $case = $this->getRepo()->getReference(
                Entity\Cases\Cases::class,
                $command->getCase()
            );

            $entity->setCase($case);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_case'));
        }

        if ($command->getLicence() !== null) {

            $licence = $this->getRepo()->getReference(
                Entity\Licence\Licence::class,
                $command->getLicence()
            );

            $entity->setLicence($licence);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_lic'));
        }

        if ($command->getOrganisation() !== null) {

            $org = $this->getRepo()->getReference(
                Entity\Licence\Licence::class,
            );

            $entity->setOrganisation($org);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_org'));
        }

        if ($command->getTransportManager() !== null) {

            $transportManager = $this->getRepo()->getReference(
                Entity\TM\TransportManager::class,
                $command->getTransportManager()
            );

            $entity->setTransportManager($transportManager);
            $entity->setNoteType($this->getRepo()->getReference(RefData::class, 'note_t_tm'));
        }

        $entity->setUser(
            $this->getRepo()->getReference(Entity\Application\Application::class,
                $this->hardCodedUserId)
        );

        return $entity;
    }

    /**
     * @var String
     * @Transfer\Filter({"name":"Zend\Filter\StringTrim"})
     * @Transfer\Validator({
     *     "name":"Zend\Validator\InArray",
     *     "options": {
     *          "haystack": {
     *              "note_t_app",
     *              "note_t_bus",
     *              "note_t_case",
     *              "note_t_lic",
     *              "note_t_org",
     *              "note_t_person",
     *              "note_t_tm"
     *          }
     *      }
     * })
     */
    protected $noteType;
}
