<?php

namespace Dvsa\Olcs\Api\Domain\Command\Email;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

class SendInsolvencyFailureList extends AbstractCommand
{
    /**
     * @var string[]
     */
    protected $organisationNumbers;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var
     */
    protected $emailSubject;

    /**
     * @return string[]
     */
    public function getOrganisationNumbers(): array
    {
        return $this->organisationNumbers;
    }

    /**
     * @param string[] $organisationNumbers
     */
    public function setOrganisationNumbers(array $organisationNumbers): void
    {
        $this->organisationNumbers = $organisationNumbers;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress
     */
    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return mixed
     */
    public function getEmailSubject()
    {
        return $this->emailSubject;
    }

    /**
     * @param mixed $emailSubject
     */
    public function setEmailSubject($emailSubject): void
    {
        $this->emailSubject = $emailSubject;
    }
}
