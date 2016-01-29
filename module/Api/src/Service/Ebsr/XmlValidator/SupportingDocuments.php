<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\XmlValidator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Exception;

/**
 * Class SupportingDocuments
 * @package Olcs\Ebsr\Validator\Structure
 */
class SupportingDocuments extends AbstractValidator
{
    const MISSING_DOCUMENT_ERROR = 'missing-document-error';

    /**
     * @var array
     */
    protected $messageTemplates = [
        self::MISSING_DOCUMENT_ERROR => 'Document %value% specified in tag %tagName% not found'
    ];

    /**
     * @var array
     */
    protected $messageVariables = [
        'tagName'
    ];

    /**
     * @var string
     */
    protected $tagName = '';
    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param  mixed $value
     * @param  array $context
     * @return bool
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value, $context = [])
    {
        $this->abstractOptions['messages'] = [];
        $dir = dirname($context['xml_filename']) . DIRECTORY_SEPARATOR;

        $this->checkFileExistsByTag($dir, $value, 'DocumentUri');
        $this->checkFileExistsByTag($dir, $value, 'SchematicMap');

        if (empty($this->abstractOptions['messages'])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $dir
     * @param \DomDocument $domDocument
     * @param string $tagName
     */
    protected function checkFileExistsByTag($dir, $domDocument, $tagName)
    {
        foreach ($domDocument->getElementsByTagName($tagName) as $document) {
            //validate exists
            $value = $document->nodeValue;
            if (!file_exists($dir . $value)) {
                $this->tagName = $tagName;
                $this->abstractOptions['messages'][] = $this->createMessage(self::MISSING_DOCUMENT_ERROR, $value);
            }
        }
    }
}
