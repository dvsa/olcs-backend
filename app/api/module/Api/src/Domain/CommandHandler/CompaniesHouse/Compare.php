<?php

/**
 * Companies House Compare
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\CompaniesHouse\CreateAlert as CreateAlertCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlert as AlertEntity;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseAlertReason as ReasonEntity;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany as CompanyEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class Compare extends AbstractCommandHandler
{
    protected $extraRepos = ['CompaniesHouseAlert', 'Organisation'];

    /**
     * Given a company number, looks up data via Companies House API and
     * checks for differences with last-stored data
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $companyNumber = $command->getCompanyNumber();
        $result = new Result();

        $apiResult = $this->api->getCompanyProfile($companyNumber, true);

        if (empty($apiResult['company_number'])) {
            $result->merge(
                $this->createAlert(
                    [AlertEntity::REASON_INVALID_COMPANY_NUMBER],
                    $companyNumber
                )
            );
            return $result;
        }

        $data = $this->normaliseProfileData($apiResult);

        try {
            // @todo watch for caching problems here if long-running queue process
            $stored = $this->getRepo()->getLatestByCompanyNumber($companyNumber);
        } catch (NotFoundException $e) {
            // Company not previously stored, save new data and return
            $company = new CompanyEntity($data);
            $this->getRepo()->save($company);
            $result
                ->addId('companiesHouseCompany', $company->getId())
                ->addMessage('Saved new company');
            return $result;
        }

        $reasons = $this->compare($stored->toArray(), $data);

        if (empty($reasons)) {
            // return early if no changes detected
            $result->addMessage('No changes');
            return $result;
        }

        $result->merge($this->createAlert($reasons, $companyNumber));

        $company = new CompanyEntity($data);
        $this->getRepo()->save($company);
        $result
            ->addId('companiesHouseCompany', $company->getId())
            ->addMessage('Saved company');

        return $result;
    }

    /**
     * @param array $old stored company data
     * @param array $new new company data
     * @return array - list of reason codes, empty if no changes
     */
    protected function compare($old, $new)
    {
        $changes = [];

        if ($this->statusHasChanged($old, $new)) {
            $changes[] = AlertEntity::REASON_STATUS_CHANGE;
        }

        if ($this->nameHasChanged($old, $new)) {
            $changes[] = AlertEntity::REASON_NAME_CHANGE;
        }

        if ($this->addressHasChanged($old, $new)) {
            $changes[] = AlertEntity::REASON_ADDRESS_CHANGE;
        }

        if ($this->peopleHaveChanged($old, $new)) {
            $changes[] = AlertEntity::REASON_PEOPLE_CHANGE;
        }

        return $changes;
    }

    /**
     * @param array $reasons
     * @param string $companyNumber
     * @return Result
     */
    protected function createAlert($reasons, $companyNumber)
    {
        $command = CreateAlertCmd::create(
            [
                'companyNumber' => $companyNumber,
                'reasons' => $reasons,
            ]
        );
        return $this->handleSideEffect($command);
    }

    // comparison functions....

    /**
     * @param array $old stored company data
     * @param array $new new company data
     * @return boolean
     */
    protected function statusHasChanged($old, $new)
    {
        return ($new['companyStatus'] !== $old['companyStatus']);
    }

    /**
     * @param array $old stored company data
     * @param array $new new company data
     * @return boolean
     */
    protected function nameHasChanged($old, $new)
    {
        return (trim(strtolower($new['companyName'])) !== trim(strtolower($old['companyName'])));
    }

    /**
     * @param array $old stored company data
     * @param array $new new company data
     * @return boolean
     */
    protected function addressHasChanged($old, $new)
    {
         $fields = [
            "addressLine1",
            "addressLine2",
            "locality",
            "poBox",
            "postalCode",
            "premises",
            "region",
        ];

        foreach ($fields as $field) {
            // check for changes to fields we already have
            if (!is_null($old[$field])) {
                if (!isset($new[$field])) {
                    // field has been deleted
                    return true;
                }
                if (trim(strtolower($new[$field])) !== trim(strtolower($old[$field]))) {
                    // field has changed!
                    return true;
                }
            }

            // check for new fields that have been added
            if (isset($new[$field]) && trim(strtolower($new[$field])) !== trim(strtolower($old[$field]))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Array comparison of officer data
     */
    protected function peopleHaveChanged($old, $new)
    {
        $old = $this->getNormalisedPeople($old);
        $new = $this->getNormalisedPeople($new);

        if (count($old) !== count($new)) {
            return true;
        }

        foreach ($old as $key => $officer) {
            if ($new[$key] != $officer) {
                return true;
            }
        }

        return false;
    }

    protected function getNormalisedPeople($data)
    {
        return array_map(
            function ($officer) {
                $dob = $officer['dateOfBirth'];
                if (is_array($dob)) {
                    $dob = sprintf(
                        '%s-%02d-%02d',
                        $dob['year'],
                        $dob['month'],
                        isset($dob['day']) ? $dob['day'] : 1
                    );
                } elseif (is_object($dob)) {
                    $dob = $dob->format('Y-m-d');
                }
                return [
                    'name' => $officer['name'],
                    'role' => $officer['role'],
                    'dateOfBirth' => $dob,
                ];
            },
            $data['officers']
        );
    }
}
