
    /**
     * Set the comment
     *
     * @param string $comment
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get the comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
