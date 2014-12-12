<?php
// src/Academic/ProjectBundle/Tests/Unit/Entity/ProjectTest.php
namespace src\Academic\ProjectBundle\Tests\Unit\Entity;

use Academic\ProjectBundle\Entity\Project;
use Academic\ProjectBundle\Entity\Issue;
use Academic\UserBundle\Entity\User;

class ProjectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Project();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        return array(
            array('name', 'Test Name'),
            array('summary', 'Test Summary')
        );
    }

    public function testParticipantAndIssues()
    {
        $projectInitial = new Project();
        $project = $projectInitial;

        $user = new User();
        $user->setUsername('test') ;
        $issue = new Issue();
        $issue->setCode('12');

        $this->assertEmpty($project->getParticipant());
        $this->assertEmpty($project->getIssues());

        $project->addParticipant($user);
        $project->addIssue($issue);

        $this->assertContains($user, $project->getParticipant());
        $this->assertContains($issue, $project->getIssues());

        $project->removeParticipant($user);
        $project->removeIssue($issue);

        $this->assertEquals($projectInitial, $project);

    }
}