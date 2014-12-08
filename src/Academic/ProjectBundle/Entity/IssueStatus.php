<?php

// src/Academic/Bundle/ProjectBundle/Entity/IssueStatus.php
namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="issue_status")
 * @ORM\Entity()
 */
class IssueStatus
{

    const CODE_CLOSED = 'CLOSED';
    const CODE_INPROGRESS= 'IN_PROGRESS';
    const CODE_OPENED = 'OPENED';
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="status_code", type="string", length=20, unique=true)
     */
    private $status_code;

    /**
     * @ORM\Column(name="label", type="string", length=20, unique=true)
     */
    private $label;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status_code
     *
     * @param string $statusCode
     * @return IssueStatus
     */
    public function setStatusCode($statusCode)
    {
        $this->status_code = $statusCode;

        return $this;
    }

    /**
     * Get status_code
     *
     * @return string 
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return IssueStatus
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }
}
