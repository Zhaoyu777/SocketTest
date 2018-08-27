<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\Admin;

class UserTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    // public function testGetUserName()
    // {
    // 	$user = new User;
    // 	$userName = $user->getUserName();

    //     $this->assertEquals($userName, "hzy");
    // }

    public function testProducerFirst()
    {
        $this->assertTrue(true);
        return 'first';
    }

    //@depends 标注来表达依赖关系
    public function testEmpty()
    {
        $stack = [];
        $this->assertEmpty($stack);

        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(array $stack)
    {
        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertNotEmpty($stack);

        return $stack;
    }

    /**
     * @depends testPush
     */
    public function testPop(array $stack)
    {
        $this->assertEquals('foo', array_pop($stack));
        $this->assertEmpty($stack);
    }



    //2.4: 有多重依赖的测试
    public function testProducerSecond()
    {
        $this->assertTrue(true);
        return 'second';
    }

    public function testProducerSecond1()
    {
        $this->assertTrue(true);
        return 'second';
    }
    /**
     * @depends testProducerFirst
     * @depends testProducerSecond
     * @depends testProducerSecond1
     */
    //public function testConsumer()
    //{	//func_get_args();获取函数的所有参数
    //    $this->assertEquals(
    //        ['first', 'second', 'second'],
    //        func_get_args()
    //    );
    //}

    //数据供给器
    //2.5: 使用返回数组的数组的数据供给器

    /**
     * @dataProvider additionProvider
     */
    public function testAdd($a, $b, $expected)
    {
        $this->assertEquals($expected, $a + $b);
    }

    public function additionProvider()
    {
        return [
            'adding zeros'  => [0, 0, 0],
            'zero plus one' => [0, 1, 1],
            'one plus zero' => [1, 0, 1],
            'one plus one'  => [1, 1, 2]
        ];
    }

    public function testCreateUser()
    {
    	$user = new Admin;
    	$userName = $user->createUser();

    	$this->assertEquals($userName, true);
    }
}
