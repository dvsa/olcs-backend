<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\TransExchange;

use Olcs\XmlTools\Xml\TemplateBuilder;

/**
 * Class Template
 * @package Olcs\Ebsr\Service\TransExchange
 */
class Template
{
    /**
     * @var TemplateBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    protected $templateFile;

    /**
     * @var array
     */
    protected $variables;

    /**
     * @return TemplateBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * @param TemplateBuilder $builder
     */
    public function setBuilder($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @return mixed
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * @param mixed $variables
     */
    public function setVariables($variables)
    {
        $this->variables = $variables;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->getBuilder()->buildTemplate($this->getTemplateFile(), $this->getVariables());
    }
}
