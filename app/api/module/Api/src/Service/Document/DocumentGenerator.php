<?php

/**
 * Document Generator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Api\Service\File\File;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;
use Dvsa\Olcs\Api\Domain\QueryHandlerManager;
use Dvsa\Olcs\DocumentShare\Service\Client;

/**
 * Document Generator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentGenerator implements FactoryInterface, NamingServiceAwareInterface
{
    use NamingServiceAwareTrait;

    /**
     * Hold an in memory cache of templates fetched from the store;
     * Useful when multiple copies of the same template are printed
     * during a single request
     */
    private $templateCache = [];

    /**
     * @var Document
     */
    private $documentService;

    /**
     * @var QueryHandlerManager
     */
    private $queryHandlerManager;

    /**
     * @var ContentStoreFileUploader
     */
    private $uploader;

    /**
     * @var Client
     */
    private $contentStore;

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\Document
     */
    private $documentRepo;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->setNamingService($serviceLocator->get('DocumentNamingService'));
        $this->documentService = $serviceLocator->get('Document');
        $this->queryHandlerManager = $serviceLocator->get('QueryHandlerManager');
        $this->uploader = $serviceLocator->get('FileUploader');
        $this->contentStore = $serviceLocator->get('ContentStore');
        $this->documentRepo = $serviceLocator->get('RepositoryServiceManager')->get('Document');

        return $this;
    }

    /**
     * Helper method to generate a string of content from a given template and
     * query parameters
     *
     * @param string $template
     * @param array $queryData
     * @param array $knownValues
     *
     * @return string
     */
    public function generateFromTemplate($template, $queryData = [], $knownValues = [])
    {
        // if template is an int then assume it is an ID to a document ID
        if (is_int($template)) {
            try {
                /* @var $documentTemplate \Dvsa\Olcs\Api\Entity\Doc\DocTemplate */
                $documentTemplate = $this->documentRepo->fetchById($template);
            } catch (\Dvsa\Olcs\Api\Domain\Exception\NotFoundException $e) {
                throw new \Exception('Template not found');
            }
            $possibleTemplatePaths = [$documentTemplate->getIdentifier()];
        } else {
            $templateWithPrefix = '/templates/' . $this->addTemplatePrefix($queryData, $template) . '.rtf';
            $templateWithoutPrefix = '/templates/' . $template . '.rtf';

            $possibleTemplatePaths = [
                $template => $template,
                $templateWithPrefix => $templateWithPrefix,
                $templateWithoutPrefix => $templateWithoutPrefix
            ];
        }

        return $this->generateFromTemplateIdentifier($possibleTemplatePaths, $queryData, $knownValues);
    }

    /**
     * Upload the generated content
     *
     * @param $content
     * @param $fileName
     * @return \Dvsa\Olcs\Api\Service\File\File
     */
    public function uploadGeneratedContent($content, $fileName)
    {
        $file = new File();
        $file->setContent($content);

        $this->uploader->setFile($file);

        return $this->uploader->upload($fileName);
    }

    /**
     * @param array $possibleTemplatePaths
     * @param array $queryData
     * @param array $knownValues
     * @return mixed
     * @throws \Exception
     */
    private function generateFromTemplateIdentifier(array $possibleTemplatePaths, $queryData = [], $knownValues = [])
    {
        $file = $this->getTemplate($possibleTemplatePaths);

        if ($file === null) {
            throw new \Exception('Template not found');
        }

        $queries = $this->documentService->getBookmarkQueries($file, $queryData);

        $result = [];

        foreach ($queries as $token => $query) {
            if ($query instanceof QueryInterface) {
                try {
                    $result[$token] = $this->queryHandlerManager->handleQuery($query);
                } catch (\Exception $ex) {
                    throw new \Exception('Error fetching data for bookmark: ' . $token . ': ' . $ex->getMessage());
                }
            } elseif (is_array($query)) {
                $list = [];
                foreach ($query as $qry) {
                    $list[] = $this->queryHandlerManager->handleQuery($qry);
                }
                $result[$token] = $list;
            }
        }
        $result = ArrayUtils::merge($result, $knownValues, true);

        return $this->documentService->populateBookmarks($file, $result);
    }

    /**
     * Add the template prefix
     *
     * @param $queryData
     * @param $template
     * @return string
     */
    private function addTemplatePrefix($queryData, $template)
    {
        foreach (['application', 'licence'] as $key) {

            if (isset($queryData[$key])) {

                if ($key === 'licence') {
                    $result = $this->queryHandlerManager
                        ->handleQuery(LicenceBundle::create(['id' => $queryData[$key]]));
                } else {
                    $result = $this->queryHandlerManager
                        ->handleQuery(ApplicationBundle::create(['id' => $queryData[$key]]));
                }

                return $this->getPrefix($result['niFlag']) . '/' . $template;
            }
        }

        return $template;
    }

    /**
     * Get the template prefix
     *
     * @param $niFlag
     * @return string
     */
    private function getPrefix($niFlag)
    {
        return $niFlag === 'N' ? 'GB' : 'NI';
    }

    /**
     * Grab the template from the possible paths
     *
     * @param array $possibleTemplatePaths
     * @return string|null
     */
    private function getTemplate(array $possibleTemplatePaths)
    {
        foreach ($possibleTemplatePaths as $template) {
            $file = $this->contentStore->read($template);
            if ($file !== null) {
                return $file;
            }
            // @todo don't uncomment it, this needs future investigation @see OLCS-13786
            /*
            if (!isset($this->templateCache[$template])) {
                $this->templateCache[$template] = $this->contentStore->read($template);
            }

            if ($this->templateCache[$template] !== null) {
                return $this->templateCache[$template];
            }
            */        }

        return null;
    }
}
