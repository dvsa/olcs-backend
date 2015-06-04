<?php

/**
 * Create IrfoPsvAuth
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Irfo;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthNumber;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\Irfo\CreateIrfoPsvAuth as Cmd;

/**
 * Create IrfoPsvAuth
 */
final class CreateIrfoPsvAuth extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrfoPsvAuth';

    protected $extraRepos = ['IrfoPsvAuthNumber'];

    public function handleCommand(CommandInterface $command)
    {
        try {
            $this->getRepo()->beginTransaction();

            // create and save a record
            $irfoPsvAuth = $this->createIrfoPsvAuthObject($command);
            $this->getRepo()->save($irfoPsvAuth);

            // deal with IrfoFileNo
            $irfoFileNo = sprintf(
                '%s/%d',
                $irfoPsvAuth->getIrfoPsvAuthType()->getSectionCode(),
                $irfoPsvAuth->getId()
            );
            $irfoPsvAuth->setIrfoFileNo($irfoFileNo);
            $this->getRepo()->save($irfoPsvAuth);

            // deal with IrfoPsvAuthNumbers
            foreach ($command->getIrfoPsvAuthNumbers() as $irfoPsvAuthNumber) {
                if (!empty($irfoPsvAuthNumber['name'])) {
                    // create
                    $irfoPsvAuthNumberEntity = new IrfoPsvAuthNumber($irfoPsvAuth, $irfoPsvAuthNumber['name']);
                    $this->getRepo('IrfoPsvAuthNumber')->save($irfoPsvAuthNumberEntity);
                }
            }

            $result = new Result();
            $result->addId('irfoPsvAuth', $irfoPsvAuth->getId());
            $result->addMessage('IRFO PSV Auth created successfully');

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
    }

    /**
     * @param Cmd $command
     * @return IrfoPsvAuth
     */
    private function createIrfoPsvAuthObject(Cmd $command)
    {
        $organisation = $this->getRepo()->getReference(Organisation::class, $command->getOrganisation());
        $type = $this->getRepo()->getReference(IrfoPsvAuthType::class, $command->getIrfoPsvAuthType());
        $status = $this->getRepo()->getRefdataReference($command->getStatus());

        $irfoPsvAuth = new IrfoPsvAuth($organisation, $type, $status);

        // set IRFO file number as it's not nullable
        // and it requires the entity to be saved first before it can be set correctly
        // as entity id is part of the IrfoFileNo
        $irfoPsvAuth->setIrfoFileNo('');

        $irfoPsvAuth->setValidityPeriod($command->getValidityPeriod());
        $irfoPsvAuth->setServiceRouteFrom($command->getServiceRouteFrom());
        $irfoPsvAuth->setServiceRouteTo($command->getServiceRouteTo());
        $irfoPsvAuth->setJourneyFrequency($command->getJourneyFrequency());
        $irfoPsvAuth->setIsFeeExemptApplication($command->getIsFeeExemptApplication());
        $irfoPsvAuth->setIsFeeExemptAnnual($command->getIsFeeExemptAnnual());
        $irfoPsvAuth->setExemptionDetails($command->getExemptionDetails());
        $irfoPsvAuth->setCopiesRequired($command->getCopiesRequired());
        $irfoPsvAuth->setCopiesRequiredTotal($command->getCopiesRequiredTotal());

        if ($command->getJourneyFrequency() !== null) {
            $irfoPsvAuth->setJourneyFrequency($this->getRepo()->getRefdataReference($command->getJourneyFrequency()));
        }

        if ($command->getInForceDate() !== null) {
            $irfoPsvAuth->setInForceDate(new \DateTime($command->getInForceDate()));
        }

        if ($command->getExpiryDate() !== null) {
            $irfoPsvAuth->setExpiryDate(new \DateTime($command->getExpiryDate()));
        }

        if ($command->getApplicationSentDate() !== null) {
            $irfoPsvAuth->setApplicationSentDate(new \DateTime($command->getApplicationSentDate()));
        }

        if ($command->getCountrys() !== null) {
            $countries = [];

            foreach ($command->getCountrys() as $countryId) {
                $countries[] = $this->getRepo()->getReference(Country::class, $countryId);
            }

            $irfoPsvAuth->setCountrys($countries);
        }

        return $irfoPsvAuth;
    }
}
