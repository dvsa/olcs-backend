<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Permits\AnswersSummary\AnswersSummaryGenerator;
use Dvsa\Olcs\Transfer\Query\Qa\AnswersSummary as AnswersSummaryQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Answers summary
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class AnswersSummary extends AbstractQueryHandler
{
    /** @var AnswersSummaryGenerator */
    private $answersSummaryGenerator;

    protected $repoServiceName = 'IrhpApplication';

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->answersSummaryGenerator = $mainServiceLocator->get('PermitsAnswersSummaryGenerator');
        $this->translator = $mainServiceLocator->get('translator');

        return parent::createService($serviceLocator);
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
        $previousLocale = $this->translator->getLocale();
        $locale = $query->getTranslateToWelsh() == 'Y' ? 'cy_GB' : 'en_GB';
        $this->translator->setLocale($locale);

        $irhpApplication = $this->getRepo()->fetchUsingId($query);
        $answersSummary = $this->answersSummaryGenerator->generate($irhpApplication);
        $representation = $answersSummary->getRepresentation();

        $this->translator->setLocale($previousLocale);

        return $representation;
    }
}
