<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 26.06.17
 * Time: 13:01
 */

use PHPUnit\Framework\TestCase;

class MainTest extends TestCase
{
    const TEST_QUERY = "match (n:NodeLable {
  string: 'string',
  int:10,
  float: 50.50505050000000,
  float2: '50,40',
  true: true,
  false: false
})
return n limit 2;";

    public function testMain()
    {
        $instance = new \Greenelf\LaravelCypher\LaravelCypher('defoult_dir');
        $query = $instance->createQuery('test', [
            'float2' => '50,40',
            'string' => 'string',
            'float' => 50.5050505,
            'int' => 10,
            'true' => true,
            'false' => false
        ]);

        $this->assertTrue($query == self::TEST_QUERY);
    }
}