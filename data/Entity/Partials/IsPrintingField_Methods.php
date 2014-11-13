
    /**
     * Set the is printing
     *
     * @param string $isPrinting
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIsPrinting($isPrinting)
    {
        $this->isPrinting = $isPrinting;

        return $this;
    }

    /**
     * Get the is printing
     *
     * @return string
     */
    public function getIsPrinting()
    {
        return $this->isPrinting;
    }
