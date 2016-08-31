<?php

namespace Dvsa\Olcs\Api\Service\Document\Bookmark;

use Dvsa\Olcs\Api\Service\Document\Bookmark\Base\DynamicBookmark;
use Dvsa\Olcs\Api\Domain\Query\Bookmark\PublicationBundle as Qry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * AbstractPublicationLinkSection
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
abstract class AbstractPublicationLinkSection extends DynamicBookmark
{
    const CLASS_NAMESPACE = __NAMESPACE__; // do not change/override this.
    const PUBLICATION_SECTION = null; // example

    const PUB_CONTENT_LINE = 'PubContentLine';
    const TABLE_ROW_1 = 'TanTableRow1';
    const TABLE_ROW_2 = 'TanTableRow2';
    const TABLE_ROW_3 = 'TanTableRow3';

    const PUB_SECTION_18 = 18;

    /** @var array */
    protected $pubTypeSection = [];
    /** @var string */
    protected $snippedPath;

    /**
     * Publication section bookmarks matched with the correct snippets
     *
     * @var array
     */
    protected $bookmarkSnippets = [
        'Section11' => [self::TABLE_ROW_1],
        'Section12' => [self::TABLE_ROW_1],
        'Section21' => [self::TABLE_ROW_1],
        'Section22' => [self::TABLE_ROW_1],
        'Section23' => [self::TABLE_ROW_1],
        'Section24' => [self::TABLE_ROW_1],
        'Section25' => [self::TABLE_ROW_1],
        'Section26' => [self::TABLE_ROW_1],
        'Section27' => [self::TABLE_ROW_1],
        'Section29' => [self::TABLE_ROW_1],
        'Section31' => [self::TABLE_ROW_1],
        'Section32' => [self::TABLE_ROW_1],
        'Section33' => [self::TABLE_ROW_1],
        'Section34' => [self::TABLE_ROW_1],
        'Section35' => [self::TABLE_ROW_1],
        'Section36' => [self::TABLE_ROW_1],
        'Section41' => [self::TABLE_ROW_1],
        'Section42' => [self::TABLE_ROW_1],
        'Section51' => [self::TABLE_ROW_2],
        'Section52' => [self::TABLE_ROW_2],
        'Section53' => [self::TABLE_ROW_2],
        'Section54' => [self::TABLE_ROW_2],
        'Section61' => [self::TABLE_ROW_1],
        'Section71' => [self::TABLE_ROW_1],
        'Section72' => [self::TABLE_ROW_1],
        'Section81' => [self::TABLE_ROW_1],
    ];

    /**
     * Gets the publication section
     *
     * @return array
     */
    public function getPubTypeSection()
    {
        return $this->pubTypeSection;
    }

    /**
     * Gets the list of bookmark snippets
     *
     * @return array
     */
    public function getBookmarkSnippets()
    {
        return $this->bookmarkSnippets;
    }

    /**
     * Gets the correct snippets based on the class name
     *
     * @param string $className Class FQCN
     *                          
     * @return array
     */
    public function getBookmarkSnippetsByClass($className)
    {
        $returnSnippets = [];

        $bookmarkSnippets = $this->getBookmarkSnippets();

        if (isset($bookmarkSnippets[$className])) {
            $fileExt = $this->getParser()->getFileExtension();

            $snippets = $bookmarkSnippets[$className];
            $snippets[] = static::PUB_CONTENT_LINE;

            foreach ($snippets as $snippetName) {
                $returnSnippets[] = file_get_contents(
                    ($this->snippedPath ?: __DIR__ . '/Snippet/') . $snippetName . '.' . $fileExt
                );
            }
        }

        return $returnSnippets;
    }

    /**
     * Query to retrieve data
     *
     * @param array $data Query parameters
     *
     * @return QueryInterface
     */
    public function getQuery(array $data)
    {
        $bundle = [
            'publicationLinks' => [
                'publicationSection'
            ]
        ];
        return Qry::create(['id' => $data['publicationId'], 'bundle' => $bundle]);
    }

    /**
     * Renders the bookmark
     *
     * @return string
     */
    public function render()
    {
        $entries = [];

        $pubSection = $this->getPubTypeSection();
        $sectionId = $pubSection[$this->data['pubType']];

        if (!is_array($sectionId)) {
            $sectionId = [$sectionId];
        }

        foreach ($this->data['publicationLinks'] as $key => $entry) {
            if (in_array($entry['publicationSection']['id'], $sectionId)) {
                /**
                 * @todo special case for section id 18, fixes the problem in olcs-11399, but a bit untidy.
                 *
                 * Fixing properly requires ETL changes and the text for schedule 4/1 true (pub section 18) to go into
                 * text3 instead of text1. This will be done in olcs-12569
                 */
                if ($entry['publicationSection']['id'] === self::PUB_SECTION_18) {
                    $entry['text3'] = $entry['text1'];
                    $entry['text1'] = null;
                }

                $entries[] = [
                    'ITEM1' => $entry['text1'],
                    'ITEM2' => $entry['text2'],
                    'ITEM3' => $entry['text3'],
                ];
            }
        }

        if (empty($entries)) {
            return 'No entries';
        }

        return $this->renderSnippets($entries);
    }

    /**
     * Renders individual snippets
     *
     * @param array $snippets Snippets
     *
     * @return string
     */
    protected function renderSnippets($snippets)
    {
        $parser  = $this->getParser();

        $class = explode('\\', get_called_class());
        $className = end($class);

        $snippetFiles = $this->getBookmarkSnippetsByClass($className);

        // at last, we can loop through each group and run a sub
        // replacement on its tokens
        $str = '';

        foreach ($snippetFiles as $snippetFile) {
            foreach ($snippets as $tokens) {
                $str .= $parser->replace($snippetFile, $tokens);
            }
        }

        return $str;
    }
}
