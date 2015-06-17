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
        /** @var Document $documentService */
        $documentService = $this->getServiceLocator()->get('Document');

        $file = $this->getTemplate($template);

        $queries = $documentService->getBookmarkQueries($file, $queryData);

        $result = [];

        foreach ($queries as $token => $query) {
            if ($query instanceof QueryInterface) {
                $result[$token] = $this->getServiceLocator()->get('QueryHandlerManager')->handleQuery($query);
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

    public function uploadGeneratedContent($content, $folder, $filename)
    {
        $uploader = $this->getServiceLocator()->get('FileUploader');
        $uploader->setFile(['content' => $content]);

        /*
         * @NOTE: setting the filepath of the identifier conflicts
         * with the need to store files uniquely (which the uploader
         * will otherwise take care of). As per discussions 13/02/15
         * we've agreed not to set "friendly" file paths and that
         * a separate task is needed to identify a solution
        $filePath = $this->getServiceLocator()
            ->get('Helper\Date')
            ->getDate('YmdHi') . '_' . $filename . '.rtf';
         */

        return $uploader->upload($folder);
    }

    /**
     * Generate and store a document
     *
     * @param string $template    Document template name
     * @param string $description Not used
     * @param array  $queryData
     * @param array  $knownValues
     *
     * @return \Common\Service\File\File
     */
    public function generateAndStore($template, $description, $queryData = [], $knownValues = [])
    {
        $template = $this->addTemplatePrefix($queryData, $template);

        $content = $this->generateFromTemplate($template, $queryData, $knownValues);

        return $this->uploadGeneratedContent($content, 'documents', $description);
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
                ->read('/templates/' . $template . '.rtf');
        }

        return $this->templateCache[$template];
    }
}
