<?php

/**
 * Generate and Upload Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Document;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Generate and Upload Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class GenerateAndUploadDocument extends AbstractCommand
{
    public $template;

    public $data;

    public $folder;
    
    public $fileName;

    public function getTemplate()
    {
        return $this->template;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getFolder()
    {
        return $this->folder;
    }

    public function getFileName()
    {
        return $this->fileName;
    }
}
