<?php

// src/Academic/Bundle/ProjectBundle/Entity/Issue.php
namespace Academic\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @ORM\Table(name="issue")
 * @ORM\Entity(repositoryClass="Academic\ProjectBundle\Entity\IssueRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Issue
{
    private $type_subtask = array(array('code' => 'SUB_TASK', 'label' => 'Subtask'));

    private $types = array(
        array('code' => 'BUG', 'label' => 'Bug'),
        array('code' => 'TASK', 'label' => 'Task'),
        array('code' => 'STORY', 'label' => 'Story'),
    );

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="issue")
     *
     */
    private $project;

    /**
     * @ORM\Column(name="code", type="string", length=30)
     */
    private $code;

    /**
     * @ORM\Column(name="summary", type="string", length=250)
     */
    private $summary;

    /**
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="type", type="string", length=30)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="IssueStatus")
     *
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="IssuePriority")
     *
     */
    private $priority;

    /**
     * @ORM\ManyToOne(targetEntity="IssueResolution")
     *
     */
    private $resolution;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $reporter;

    /**
     * @ORM\ManyToOne(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $assignee;

    /**
     * @ORM\ManyToMany(targetEntity="\Academic\UserBundle\Entity\User")
     *
     */
    private $collaborators;

    /**
     * @ORM\OneToMany(targetEntity="\Academic\ProjectBundle\Entity\Issue\Comment", mappedBy="issue")
     *
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity="\Academic\ProjectBundle\Entity\Issue\Activity", mappedBy="issue")
     *
     */
    private $activities;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="child_issues")
     *
     */
    private $parent_issue;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent_issue")
     *
     */
    private $child_issues;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     */
    private $created_at;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     *
     */
    private $updated_at;

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->created_at = new \DateTime();
    }

    /**
     * @ORM\PrePersist
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->collaborators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->child_issues = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->activities = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Issue
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
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
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Issue
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Issue
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

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set status
     *
     * @param \Academic\ProjectBundle\Entity\IssueStatus $status
     * @return Issue
     */
    public function setStatus(\Academic\ProjectBundle\Entity\IssueStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Academic\ProjectBundle\Entity\IssueStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set priority
     *
     * @param \Academic\ProjectBundle\Entity\IssuePriority $priority
     * @return Issue
     */
    public function setPriority(\Academic\ProjectBundle\Entity\IssuePriority $priority = null)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \Academic\ProjectBundle\Entity\IssuePriority
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set resolution
     *
     * @param \Academic\ProjectBundle\Entity\IssueResolution $resolution
     * @return Issue
     */
    public function setResolution(\Academic\ProjectBundle\Entity\IssueResolution $resolution = null)
    {
        $this->resolution = $resolution;

        return $this;
    }

    /**
     * Get resolution
     *
     * @return \Academic\ProjectBundle\Entity\IssueResolution
     */
    public function getResolution()
    {
        return $this->resolution;
    }

    /**
     * Set reporter
     *
     * @param \Academic\UserBundle\Entity\User $reporter
     * @return Issue
     */
    public function setReporter(\Academic\UserBundle\Entity\User $reporter = null)
    {
        $this->reporter = $reporter;

        return $this;
    }

    /**
     * Get reporter
     *
     * @return \Academic\UserBundle\Entity\User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Set assignee
     *
     * @param \Academic\UserBundle\Entity\User $assignee
     * @return Issue
     */
    public function setAssignee(\Academic\UserBundle\Entity\User $assignee = null)
    {
        $this->assignee = $assignee;

        return $this;
    }

    /**
     * Get assignee
     *
     * @return \Academic\UserBundle\Entity\User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * Add collaborators
     *
     * @param \Academic\UserBundle\Entity\User $collaborators
     * @return Issue
     */
    public function addCollaborator(\Academic\UserBundle\Entity\User $collaborators)
    {
        $this->collaborators[] = $collaborators;

        return $this;
    }

    /**
     * Remove collaborators
     *
     * @param \Academic\UserBundle\Entity\User $collaborators
     */
    public function removeCollaborator(\Academic\UserBundle\Entity\User $collaborators)
    {
        $this->collaborators->removeElement($collaborators);
    }

    /**
     * Get collaborators
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * Set parent_issue
     *
     * @param \Academic\ProjectBundle\Entity\Issue $parentIssue
     * @return Issue
     */
    public function setParentIssue(\Academic\ProjectBundle\Entity\Issue $parentIssue = null)
    {
        $this->parent_issue = $parentIssue;

        return $this;
    }

    /**
     * Get parent_issue
     *
     * @return \Academic\ProjectBundle\Entity\Issue
     */
    public function getParentIssue()
    {
        return $this->parent_issue;
    }

    /**
     * Add child_issues
     *
     * @param \Academic\ProjectBundle\Entity\Issue $childIssues
     * @return Issue
     */
    public function addChildIssue(\Academic\ProjectBundle\Entity\Issue $childIssues)
    {
        $this->child_issues[] = $childIssues;
        $childIssues->setParentIssue($this);

        return $this;
    }

    /**
     * Remove child_issues
     *
     * @param \Academic\ProjectBundle\Entity\Issue $childIssues
     */
    public function removeChildIssue(\Academic\ProjectBundle\Entity\Issue $childIssues)
    {
        $this->child_issues->removeElement($childIssues);
    }

    /**
     * Get child_issues
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildIssues()
    {
        return $this->child_issues;
    }

    /**
     * Set project
     *
     * @param \Academic\ProjectBundle\Entity\Project $project
     * @return Issue
     */
    public function setProject(\Academic\ProjectBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Academic\ProjectBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }

    public function getTypeLabel()
    {
        $label = '';
        foreach ($this->types as $type) {
            if ($this->getType() === $type['code']) {
                $label = $type['label'];
            }
        }
        foreach ($this->type_subtask as $type) {
            if ($this->getType() === $type['code']) {
                $label = $type['label'];
            }
        }

        return $label;
    }

    public function getAvailableTypes()
    {
        return $this->types;
    }

    public function getAvailableTypesSubtask()
    {
        return $this->type_subtask;
    }

    /**
     * Add comments
     *
     * @param \Academic\ProjectBundle\Entity\Issue\Comment $comments
     * @return Issue
     */
    public function addComment(\Academic\ProjectBundle\Entity\Issue\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Academic\ProjectBundle\Entity\Issue\Comment $comments
     */
    public function removeComment(\Academic\ProjectBundle\Entity\Issue\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add activities
     *
     * @param \Academic\ProjectBundle\Entity\Issue\Activity $activities
     * @return Issue
     */
    public function addActivity(\Academic\ProjectBundle\Entity\Issue\Activity $activities)
    {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \Academic\ProjectBundle\Entity\Issue\Activity $activities
     */
    public function removeActivity(\Academic\ProjectBundle\Entity\Issue\Activity $activities)
    {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActivities()
    {
        return $this->activities;
    }
}
