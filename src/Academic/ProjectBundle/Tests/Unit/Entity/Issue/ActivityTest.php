<?php
// src/Academic/ProjectBundle/Tests/Unit/Entity/Issue/ActivityTest.php
namespace src\Academic\ProjectBundle\Tests\Unit\Entity\Issue;

use Academic\ProjectBundle\Entity\Issue;
use Academic\ProjectBundle\Entity\Issue\Activity;
use Academic\UserBundle\Entity\User;

class ActivityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Activity();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $user = new User();
        $issue = new Issue();
        $created_at = new \DateTime();
        return array(
            array('event', 'Test Event'),
            array('user', $user),
            array('issue', $issue),
            array('createdAt', $created_at)
        );
    }

}