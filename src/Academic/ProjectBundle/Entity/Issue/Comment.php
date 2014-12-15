<?php

// src/Academic/ProjectBundle/Entity/Issue/Comment.php
namespace Academic\ProjectBundle\Entity\Issue;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="issue_comment")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Comment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="body", type="string", length=30)
     */
    private $body;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\ProjectBundle\Entity\Issue", inversedBy="comments")
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
     * Set body
     *
     * @param string $body
     * @return Comment
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
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
     * @return Comment
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
