<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Si;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Nr\InrClient;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Service\Nr\InrClientInterface;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Si\SeriousInfringement as SiEntity;
use Dvsa\Olcs\Api\Service\Nr\MsiResponse as MsiResponseService;
use Dvsa\Olcs\Transfer\Command\Cases\Si\SendResponse as SendErruResponseCmd;
use Zend\Http\Response;
use Dvsa\Olcs\Api\Domain\Exception\RestResponseException;
use Zend\Http\Client\Adapter\Exception\RuntimeException as AdapterRuntimeException;

/**
 * SendResponse
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class SendResponse extends AbstractCommandHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Cases';

    protected $extraRepos = ['SeriousInfringement'];

    /**
     * @var InrClient
     */
    protected $inrClient;

    /**
     * @var MsiResponseService
     */
    protected $msiResponseService;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->inrClient = $mainServiceLocator->get(InrClientInterface::class);
        $this->msiResponseService = $mainServiceLocator->get(MsiResponseService::class);

        return parent::createService($serviceLocator);
    }

    /**
     * SendResponse
     *
     * @param CommandInterface $command
     * @throws RestResponseException
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var CasesEntity $case
         * @var SendErruResponseCmd $command
         */
        $case = $this->getRepo()->fetchById($command->getCase());

        //generate the xml to send to national register
        $xml = $this->msiResponseService->create($case);

        //here is where we would expect the response from national register.
        try {
            $responseCode = $this->inrClient->makeRequest($xml);
        } catch (AdapterRuntimeException $e) {
            throw new RestResponseException('The was an error sending the INR response');
        }

        if ($responseCode !== Response::STATUS_CODE_202) {
            throw new RestResponseException('INR Http response code was ' . $responseCode);
        }

        /** @var SiEntity $si */
        $si = $case->getSeriousInfringements()->first();

        $si->updateErruResponse(
            $this->getCurrentUser(),
            new \DateTime($this->msiResponseService->getResponseDateTime())
        );

        $this->getRepo('SeriousInfringement')->save($si);

        $result = new Result();
        $result->addMessage('Msi Response sent');
        $result->addId('case', $case->getId());
        $result->addId('serious_infringement', $si->getId());

        return $result;
    }
}
