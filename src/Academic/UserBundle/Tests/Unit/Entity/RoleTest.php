<?php
// src/Academic/UserBundle/Tests/Unit/Entity/RoleTest.php
namespace src\Academic\UserBundle\Tests\Unit\Entity;

use Academic\UserBundle\Entity\Role;

class RoleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider settersAndGettersDataProvider
     */
    public function testSettersAndGetters($property, $value)
    {
        $obj = new Role();

        call_user_func_array(array($obj, 'set' . ucfirst($property)), array($value));
        $this->assertEquals($value, call_user_func_array(array($obj, 'get' . ucfirst($property)), array()));
    }

    public function settersAndGettersDataProvider()
    {
        return array(
            array('name', 'Test role name'),
            array('role', 'TEST_ROLE')
        );
    }
}