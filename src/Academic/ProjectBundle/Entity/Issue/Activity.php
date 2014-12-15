<?php

// src/Academic/ProjectBundle/Entity/Issue/Comment.php
namespace Academic\ProjectBundle\Entity\Issue;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="issue_activity")
 * @ORM\Entity(repositoryClass="Academic\ProjectBundle\Entity\Issue\ActivityRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Activity
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="event", type="string", length=255)
     */
    private $event;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\ProjectBundle\Entity\Issue", inversedBy="activities")
     *
     */
    private $issue;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     */
    private $created_at;

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
     * Set user
     *
     * @param \Academic\UserBundle\Entity\User $user
     * @return Comment
     */
    public function setUser(\Academic\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Academic\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set issue
     *
     * @param \Academic\ProjectBundle\Entity\Issue $issue
     * @return Comment
     */
    public function setIssue(\Academic\ProjectBundle\Entity\Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \Academic\ProjectBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set event
     *
     * @param string $event
     * @return Activity
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Activity
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
