<?php
// src/Academic/ProjectBundle/Tests/Unit/Entity/Issue/CommentTest.php
namespace src\Academic\ProjectBundle\Tests\Unit\Entity\Issue;

use Academic\ProjectBundle\Entity\Issue;
use Academic\ProjectBundle\Entity\Issue\Comment;
use Academic\UserBundle\Entity\User;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Comment();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        $user = new User();
        $issue = new Issue();
        $created_at = new \DateTime();
        return array(
            array('user', $user),
            array('issue', $issue),
            array('createdAt', $created_at),
            array('body', 'Test Body')
        );
    }

}