<?php

// src/Academic/Bundle/ProjectBundle/Entity/Project.php
namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Academic\ProjectBundle\Entity\ProjectRepository")
 */
class Project
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(name="summary", type="text")
     */
    private $summary;

    /**
     * @ORM\ManyToMany(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $participant;

    /**
     * @ORM\OneToMany(targetEntity="\Academic\ProjectBundle\Entity\Issue", mappedBy="project")
     *
     */
    private $issues;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->participant = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Project
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Add participant
     *
     * @param \Academic\UserBundle\Entity\User $participant
     * @return Project
     */
    public function addParticipant(\Academic\UserBundle\Entity\User $participant)
    {
        $this->participant[] = $participant;

        return $this;
    }

    /**
     * Remove participant
     *
     * @param \Academic\UserBundle\Entity\User $participant
     */
    public function removeParticipant(\Academic\UserBundle\Entity\User $participant)
    {
        $this->participant->removeElement($participant);
    }

    /**
     * Get participant
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getParticipant()
    {
        return $this->participant;
    }

    /**
     * checks if user is project participant
     * @param $userId
     * @return bool
     */
    public function isParticipant($userId)
    {
        foreach ($this->participant as $participant){
            if ($participant->getId() == $userId)
                return true;
        }
        return false;
    }


    /**
     * Add issues
     *
     * @param \Academic\ProjectBundle\Entity\Issue $issues
     * @return Project
     */
    public function addIssue(\Academic\ProjectBundle\Entity\Issue $issues)
    {
        $this->issues[] = $issues;

        return $this;
    }

    /**
     * Remove issues
     *
     * @param \Academic\ProjectBundle\Entity\Issue $issues
     */
    public function removeIssue(\Academic\ProjectBundle\Entity\Issue $issues)
    {
        $this->issues->removeElement($issues);
    }

    /**
     * Get issues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIssues()
    {
        return $this->issues;
    }
}
