<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Email;

use DateTimeImmutable;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\EmailAwareInterface;
use Dvsa\Olcs\Api\Domain\Exception\MissingEmailException;
use Dvsa\Olcs\Api\Domain\Repository\Application as ApplicationRepo;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\System\Category;

final class SendNewMessageNotificationToOperators extends AbstractEmailHandler implements EmailAwareInterface
{
    protected $repoServiceName = ApplicationRepo::class;
    protected $template = 'messaging-new-message-operator';
    protected $subject = 'email.new-message-for-operator.subject';

    /** @param ApplicationEntity $recordObject */
    protected function getRecipients($recordObject): array
    {
        return $this->organisationRecipients($recordObject->getLicence()->getOrganisation());
    }

    /** @param ApplicationEntity $recordObject */
    protected function getTranslateToWelsh($recordObject)
    {
        return $recordObject->getLicence()->getTranslateToWelsh();
    }

    /** @param ApplicationEntity $recordObject */
    protected function createMissingEmailTask($recordObject, Result $result, MissingEmailException $exception): Result
    {
        $taskData = [
            'category'        => Category::CATEGORY_LICENSING,
            'subCategory'     => Category::TASK_SUB_CATEGORY_LICENSING_GENERAL_TASK,
            'description'     => sprintf(
                'Unable to send email - no organisation recipients found for Org: %s' .
                ' - Please update the organisation admin user contacts to ensure at least one has a valid email address.',
                $recordObject->getLicence()->getOrganisation()->getName(),
            ),
            'actionDate'      => (new DateTimeImmutable())->format('Y-m-d'),
            'licence'         => $recordObject->getLicence()->getId(),
            'irhpApplication' => $recordObject->getId(),
            'urgent'          => 'Y',
        ];

        $result->merge($this->handleSideEffect(CreateTask::create($taskData)));
        $result->addMessage($exception->getMessage());
        return $result;
    }
}
