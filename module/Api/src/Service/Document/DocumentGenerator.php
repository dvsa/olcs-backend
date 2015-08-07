<?php

/**
 * Document Generator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\ApplicationBundle;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\Bookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\LicenceBundle;
use Dvsa\Olcs\Api\Service\File\ContentStoreFileUploader;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Document Generator
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentGenerator implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Hold an in memory cache of templates fetched from the store;
     * Useful when multiple copies of the same template are printed
     * during a single request
     */
    private $templateCache = [];

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
        $template = '/templates/' . $template . '.rtf';

        return $this->generateFromTemplateIdentifier($template, $queryData, $knownValues);
    }

    public function generateFromTemplateIdentifier($template, $queryData = [], $knownValues = [])
    {
        /** @var Document $documentService */
        $documentService = $this->getServiceLocator()->get('Document');

        $file = $this->getTemplate($template);

        $queries = $documentService->getBookmarkQueries($file, $queryData);

        $result = [];

        foreach ($queries as $token => $query) {
            if ($query instanceof QueryInterface) {
                try {
                    $result[$token] = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($query);
                } catch (\Exception $ex) {
                    throw new \Exception('Error fetching data for bookmark: ' . $token . ': ' . $ex->getMessage());
                }
            } elseif (is_array($query)) {
                $list = [];
                foreach ($query as $qry) {
                    $list[] = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($qry);
                }
                $result[$token] = $list;
            }
        }

        $result = array_merge($result, $knownValues);

        return $documentService->populateBookmarks($file, $result);
    }

    public function uploadGeneratedContent($content, $folder = null, $fileName = null)
    {
        /** @var ContentStoreFileUploader $uploader */
        $uploader = $this->getServiceLocator()->get('FileUploader');

        $file = ['content' => $content];

        $uploader->setFile($file);

        return $uploader->upload($folder, $fileName);
    }

    /**
     * Generate and store a document
     *
     * @param string $template    Document template name
     * @param array  $queryData
     * @param array  $knownValues
     *
     * @return \Common\Service\File\File
     */
    public function generateAndStore($template, $queryData = [], $knownValues = [])
    {
        $template = $this->addTemplatePrefix($queryData, $template);

        $content = $this->generateFromTemplate($template, $queryData, $knownValues);

        return $this->uploadGeneratedContent($content, 'documents');
    }

    public function addTemplatePrefix($queryData, $template)
    {
        foreach (['application', 'licence'] as $key) {

            if (isset($queryData[$key])) {

                if ($key === 'licence') {
                    $result = $this->getServiceLocator()->get('QueryHandlerManager')
                        ->handleQuery(LicenceBundle::create(['id' => $queryData[$key]]));
                } else {
                    $result = $this->getServiceLocator()->get('QueryHandlerManager')
                        ->handleQuery(ApplicationBundle::create(['id' => $queryData[$key]]));
                }

                return $this->getPrefix($result['niFlag']) . '/' . $template;
            }
        }

        return $template;
    }

    private function getPrefix($niFlag)
    {
        return $niFlag === 'N' ? 'GB' : 'NI';
    }

    private function getTemplate($template)
    {
        if (!isset($this->templateCache[$template])) {

            $this->templateCache[$template] = $this->getServiceLocator()
                ->get('ContentStore')
                ->read($template);
        }

        return $this->templateCache[$template];
    }
}
