<?php

/**
 * Companies House Initial Load
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\CompaniesHouse;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\CompaniesHouse\CompaniesHouseCompany as CompanyEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\CompaniesHouse\Service\Exception as ApiException;
use Dvsa\Olcs\Api\Domain\Exception\Exception as DomainException;

/**
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class InitialLoad extends AbstractCommandHandler
{
    /**
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $companyNumber = $command->getCompanyNumber();
        $result = new Result();

        try {
            $apiResult = $this->api->getCompanyProfile($companyNumber, true);
        } catch (ApiException $e) {
            // rethrow client exception as domain exception
            throw new DomainException(
                'Failure from Companies House API: ' . $e->getMessage(),
                0,
                $e
            );
        }

        $data = $this->normaliseProfileData($apiResult);
        $company = new CompanyEntity($data);
        $this->getRepo()->save($company);

        $result
            ->addId('companiesHouseCompany', $company->getId())
            ->addMessage('Company added');

        return $result;
    }
}
