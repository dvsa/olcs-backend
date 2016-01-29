<?php

/**
 * Create Correspondence Record
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\EmailAwareTrait;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\CorrespondenceInbox;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

/**
 * Create Correspondence Record
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateCorrespondenceRecord extends AbstractCommandHandler implements EmailAwareInterface
{
    use EmailAwareTrait;

    protected $repoServiceName = 'CorrespondenceInbox';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Licence $licence */
        $licence = $this->getRepo()->getReference(Licence::class, $command->getLicence());

        $record = new CorrespondenceInbox(
            $licence,
            $this->getRepo()->getReference(Document::class, $command->getDocument())
        );

        $this->getRepo()->save($record);
        $result->addId('correspondenceInbox', $record->getId());
        $result->addMessage('Correspondence record created');

        foreach ($licence->getOrganisation()->getAdminOrganisationUsers() as $orgUser) {
            $user = $orgUser->getUser();

            $message = new \Dvsa\Olcs\Email\Data\Message(
                $user->getContactDetails()->getEmailAddress(),
                'email.licensing-information.' . $command->getType()  . '.subject'
            );
            $message->setTranslateToWelsh($user->getTranslateToWelsh());

            $this->sendEmailTemplate(
                $message,
                'licensing-information-' . $command->getType(),
                [
                    'licNo' => $licence->getLicNo(),
                    // @NOTE the http://selfserve part gets replaced
                    'url' => 'http://selfserve/correspondence'
                ]
            );
        }

        $result->addMessage('Email sent');

        return $result;
    }
}
