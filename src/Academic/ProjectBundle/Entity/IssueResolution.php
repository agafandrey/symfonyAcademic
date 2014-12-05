<?php

// src/Academic/Bundle/ProjectBundle/Entity/IssueResolution.php
namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="issue_resolution")
 * @ORM\Entity()
 */
class IssueResolution
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="resolution_code", type="string", length=20, unique=true)
     */
    private $resolution_code;

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
     * Set resolution_code
     *
     * @param string $resolutionCode
     * @return IssueResolution
     */
    public function setResolutionCode($resolutionCode)
    {
        $this->resolution_code = $resolutionCode;

        return $this;
    }

    /**
     * Get resolution_code
     *
     * @return string 
     */
    public function getResolutionCode()
    {
        return $this->resolution_code;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return IssueResolution
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
