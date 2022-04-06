<?php

namespace Dvsa\Olcs\AcquiredRights\Model;

use DateTimeImmutable;

final class ApplicationReference
{
    public const APPLICATION_STATUS_SUBMITTED = 'SUBMITTED';
    public const APPLICATION_STATUS_UNDER_CONSIDERATION = 'UNDER_CONSIDERATION';
    public const APPLICATION_STATUS_APPROVED = 'APPROVED';
    public const APPLICATION_STATUS_DECLINED = 'DECLINED';
    public const APPLICATION_STATUS_APPROVED_AFTER_APPEAL = 'APPROVED_AFTER_APPEAL';
    public const APPLICATION_STATUS_DECLINED_AFTER_APPEAL = 'DECLINED_AFTER_APPEAL';
    public const APPLICATION_STATUS = [
        ApplicationReference::APPLICATION_STATUS_SUBMITTED,
        ApplicationReference::APPLICATION_STATUS_UNDER_CONSIDERATION,
        ApplicationReference::APPLICATION_STATUS_APPROVED,
        ApplicationReference::APPLICATION_STATUS_DECLINED,
        ApplicationReference::APPLICATION_STATUS_APPROVED_AFTER_APPEAL,
        ApplicationReference::APPLICATION_STATUS_DECLINED_AFTER_APPEAL,
    ];

    protected string $id;
    protected string $reference;
    protected string $status;
    protected DateTimeImmutable $submittedAt;
    protected DateTimeImmutable $dateOfBirth;
    protected ?DateTimeImmutable $statusUpdateAt;

    public function __construct(
        string $id,
        string $reference,
        string $status,
        DateTimeImmutable $submittedAt,
        DateTimeImmutable $dateOfBirth,
        ?DateTimeImmutable $statusUpdateAt = null
    ) {
        $this->id = $id;
        $this->reference = $reference;
        if (!ApplicationReference::isValidApplicationStatus($status)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Status (%s) is invalid; valid values are: %s',
                    $status,
                    implode('|', self::APPLICATION_STATUS)
                )
            );
        }
        $this->status = $status;
        $this->submittedAt = $submittedAt;
        $this->dateOfBirth = $dateOfBirth;
        $this->statusUpdateAt = $statusUpdateAt;

        return $this;
    }

    public static function isValidApplicationStatus(string $status): bool
    {
        return in_array($status, ApplicationReference::APPLICATION_STATUS);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubmittedAt(): DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function getDateOfBirth(): DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function getStatusUpdateAt(): ?DateTimeImmutable
    {
        return $this->statusUpdateAt;
    }
}
