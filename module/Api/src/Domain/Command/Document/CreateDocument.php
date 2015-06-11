<?php

/**
 * Create Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Document;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Document
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateDocument extends AbstractCommand
{
    public $identifier;

    public $description;

    public $filename;
    
    public $licence;
    
    public $category;

    public $subCategory;

    public $isExternal;

    public $isReadOnly;

    public $issuedDate;

    public $size;

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getFilename()
    {
        return $this->filename;
    }

    public function getLicence()
    {
        return $this->licence;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function getSubCategory()
    {
        return $this->subCategory;
    }

    public function getIsExternal()
    {
        return $this->isExternal;
    }

    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }

    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    public function getSize()
    {
        return $this->size;
    }
}
