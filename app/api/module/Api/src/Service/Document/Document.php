<?php

namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Interfaces\DateHelperAwareInterface;
use Dvsa\Olcs\Api\Service\Document\Bookmark\Interfaces\FileStoreAwareInterface;
use Dvsa\Olcs\DocumentShare\Data\Object\File as ContentStoreFile;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Document generation service
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
class Document implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    const DOCUMENT_TIMESTAMP_FORMAT = 'YmdHi';

    public function getBookmarkQueries(ContentStoreFile $file, $data)
    {
        $queryData = [];

        /**
         * @NOTE We no longer store MIME Type, at the moment it is safe to assum RTF, however this could change
         */
        $tokens = $this->getParser('application/rtf')
            ->extractTokens($file->getContent());

        $bookmarks = $this->getBookmarks($tokens);

        foreach ($bookmarks as $token => $bookmark) {

            // we don't need to query if the bookmark is static (i.e.
            // doesn't rely on any backend information)
            if ($bookmark->isStatic()) {
                continue;
            }

            $query = $bookmark->getQuery($data);

            // we need to allow for the fact the bookmark might not want
            // to actually generate a query in which case it can return
            // a null value
            if ($query !== null) {
                $queryData[$token] = $query;
            }
        }

        return $queryData;
    }

    public function populateBookmarks(ContentStoreFile $file, $data)
    {
        $populatedData = [];

        $content = $file->getContent();

        /**
         * @NOTE We no longer store MIME Type, at the moment it is safe to assum RTF, however this could change
         */
        $parser = $this->getParser('application/rtf');
        $tokens = $parser->extractTokens($content);

        $bookmarks = $this->getBookmarks($tokens);

        foreach ($bookmarks as $token => $bookmark) {

            /**
             * Let the bookmark know what parser is currently active;
             * some may use this for sub-bookmark processing
             */
            $bookmark->setParser($parser);

            if ($bookmark->isStatic()) {

                $result = $bookmark->render();

            } elseif (isset($data[$token])) {

                $bookmark->setData($data[$token]);
                $result = $bookmark->render();

            } else {
                // no data to fulfil this dynamic bookmark, but that's okay
                $result = null;
            }

            // @TODO this check means bookmarks we did find but couldn't replace with
            // data are left in tact in the document, which can appear confusing. We
            // do this for now because of course *every* token has a bookmark, even if
            // it's a fallback TextBlock. Could modify the below to check the bookmark type...
            if ($result !== null) {
                $populatedData[$token] = [
                    'content' => $result,
                    'preformatted' => $bookmark->isPreformatted()
                ];
            }
        }

        return $parser->replace($content, $populatedData);
    }

    private function getParser($type)
    {
        $factory = new Parser\ParserFactory();
        return $factory->getParser($type);
    }

    private function getBookmarks($tokens)
    {
        $bookmarks = [];

        $factory = new Bookmark\BookmarkFactory();
        foreach ($tokens as $token) {
            $bookmark = $factory->locate($token);

            if ($bookmark instanceof DateHelperAwareInterface) {
                $bookmark->setDateHelper(
                    $this->getServiceLocator()->get('DateService')
                );
            }

            if ($bookmark instanceof FileStoreAwareInterface) {
                $bookmark->setFileStore(
                    $this->getServiceLocator()->get('ContentStore')
                );
            }

            $bookmarks[$token] = $bookmark;
        }

        return $bookmarks;
    }

    /**
     * Returns a document timestamp
     *
     * @return string
     */
    public function getTimestampFormat()
    {
        return self::DOCUMENT_TIMESTAMP_FORMAT;
    }

    /**
     * Formats a document filename
     *
     * @param string $input
     * @return string
     */
    public function formatFilename($input)
    {
        return str_replace([' ', '/'], '_', $input);
    }

    /**
     * Get uploader
     *
     * @return \Dvsa\Olcs\Api\Service\File\FileUploaderInterface
     */
    protected function getUploader()
    {
        return $this->getServiceLocator()->get('FileUploader');
    }
}
