<?php
// src/Academic/ProjectBundle/Tests/Unit/Entity/ProjectTest.php
namespace src\Academic\ProjectBundle\Tests\Unit\Entity;

use Academic\ProjectBundle\Entity\IssueStatus;

class IssueStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new IssueStatus();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        return array(
            array('statusCode', '1'),
            array('label', 'Test Label')
        );
    }
}
