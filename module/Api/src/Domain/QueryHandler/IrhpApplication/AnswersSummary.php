<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryGenerator;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\IpaAnswersSummaryGenerator;
use Dvsa\Olcs\Transfer\Query\Qa\AnswersSummary as AnswersSummaryQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\I18n\Translator\Translator;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Answers summary
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnswersSummary extends AbstractQueryHandler
{
    /** @var AnswersSummaryGenerator */
    private $answersSummaryGenerator;

    /** @var IpaAnswersSummaryGenerator */
    private $ipaAnswersSummaryGenerator;

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['IrhpPermitApplication'];

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, AnswersSummary::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|AnswersSummaryQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplication = $this->getRepo()->fetchUsingId($query);

        $answersSummaryGenerator = $this->answersSummaryGenerator;
        $entity = $irhpApplication;

        if ($irhpApplication->isBilateral()) {
            $irhpPermitApplication = $this->getRepo('IrhpPermitApplication')->fetchById($query->getIrhpPermitApplication());

            if ($irhpPermitApplication->getIrhpApplication() !== $irhpApplication) {
                throw new NotFoundException('Mismatched IrhpApplication and IrhpPermitApplication');
            }

            $answersSummaryGenerator = $this->ipaAnswersSummaryGenerator;
            $entity = $irhpPermitApplication;
        }

        $previousLocale = $this->translator->getLocale();
        $locale = $query->getTranslateToWelsh() == 'Y' ? 'cy_GB' : 'en_GB';
        $this->translator->setLocale($locale);

        $answersSummary = $answersSummaryGenerator->generate($entity);
        $representation = $answersSummary->getRepresentation();

        $this->translator->setLocale($previousLocale);

        return $representation;
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnswersSummary
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }
        $this->answersSummaryGenerator = $container->get('PermitsAnswersSummaryGenerator');
        $this->ipaAnswersSummaryGenerator = $container->get('PermitsIpaAnswersSummaryGenerator');
        $this->translator = $container->get('translator');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}
