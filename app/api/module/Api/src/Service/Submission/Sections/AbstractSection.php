<?php

namespace Dvsa\Olcs\Api\Service\Submission\Sections;

use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Zend\View\Renderer\PhpRenderer;

/**
 * Class AbstractSection
 * @package Dvsa\Olcs\Api\Service\Submission\Section
 */
abstract class AbstractSection implements SectionGeneratorInterface
{
    /**
     * @var QueryHandlerManager
     */
    private $queryHandler;

    /**
     * @var /Zend\View\Renderer\PhpRenderer
     */
    private $viewRenderer;

    public function __construct(QueryHandlerManager $queryHandler, PhpRenderer $viewRenderer)
    {
        $this->queryHandler = $queryHandler;
        $this->viewRenderer = $viewRenderer;
    }

    protected function handleQuery($query)
    {
        return $this->queryHandler->handleQuery($query);
    }

    /**
     * Extract personData if exists, used by child classes
     * @param $contactDetails
     * @return array
     */
    protected function extractPerson($contactDetails = null)
    {
        $personData = [
            'title' => '',
            'forename' => '',
            'familyName' => '',
            'birthDate' => '',
            'birthPlace' => ''
        ];

        if ($contactDetails instanceof ContactDetails && ($contactDetails->getPerson() instanceof Person)) {
            $person = $contactDetails->getPerson();

            $personData = [
                'title' => !empty($person->getTitle()) ? $person->getTitle()->getDescription() : '',
                'forename' => $person->getForename(),
                'familyName' => $person->getFamilyName(),
                'birthDate' => $this->formatDate($person->getBirthDate()),
                'birthPlace' => $person->getBirthPlace()
            ];
        }
        return $personData;
    }

    /**
     * Format a date
     *
     * @param null|mixed $datetime
     * @return string
     */
    protected function formatDate($datetime = null)
    {
        if (!empty($datetime)) {
            if (is_string($datetime)) {
                $datetime = new \DateTime($datetime);
            }

            return $datetime->format('d/m/Y');
        }

        return '';
    }

    /**
     * @return mixed
     */
    public function getViewRenderer()
    {
        return $this->viewRenderer;
    }
}
