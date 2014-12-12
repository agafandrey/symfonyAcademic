<?php
// src/Academic/ProjectBundle/Tests/Unit/Entity/ProjectTest.php
namespace src\Academic\ProjectBundle\Tests\Unit\Entity;

use Academic\ProjectBundle\Entity\Project;
use Academic\ProjectBundle\Entity\Issue;
use Academic\ProjectBundle\Entity\IssueStatus;
use Academic\ProjectBundle\Entity\IssueResolution;
use Academic\ProjectBundle\Entity\IssuePriority;
use Academic\ProjectBundle\Entity\Issue\Activity;
use Academic\ProjectBundle\Entity\Issue\Comment;
use Academic\UserBundle\Entity\User;

class IssueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Issue();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $status = new IssueStatus();
        $priority = new IssuePriority();
        $resolution = new IssueResolution();
        $reporter = new User() ;
        $assignee = new User();
        $parentIssue = new Issue();
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();
        $project = new Project();
        return array(
            array('code', 'Test Code'),
            array('summary', 'Test Summary'),
            array('description', 'Test Description'),
            array('type', 'Test Type'),
            array('status', $status),
            array('priority', $priority),
            array('resolution', $resolution),
            array('reporter', $reporter),
            array('assignee', $assignee),
            array('parentIssue', $parentIssue),
            array('createdAt', $createdAt),
            array('updatedAt', $updatedAt),
            array('project', $project),
        );
    }

    public function testCollaboratorsActivitiesChildrenComments()
    {
        $issueInitial = new Issue();
        $issue = $issueInitial;

        $user = new User();
        $user->setUsername('test_collaborator') ;
        $child = new Issue();
        $child->setCode('12');
        $activity = new Activity();
        $activity->setEvent('Test Event');
        $comment = new Comment();
        $comment->setBody('Test Comment');

        $this->assertEmpty($issue->getCollaborators());
        $this->assertEmpty($issue->getChildIssues());
        $this->assertEmpty($issue->getActivities());
        $this->assertEmpty($issue->getComments());

        $issue->addCollaborator($user);
        $issue->addChildIssue($child);
        $issue->addActivity($activity);
        $issue->addComment($comment);

        $this->assertContains($user, $issue->getCollaborators());
        $this->assertContains($child, $issue->getChildIssues());
        $this->assertContains($activity, $issue->getActivities());
        $this->assertContains($comment, $issue->getComments());

        $issue->removeCollaborator($user);
        $issue->removeChildIssue($issue);
        $issue->removeActivity($activity);
        $issue->removeComment($comment);

        $this->assertEquals($issueInitial, $issue);

    }
}