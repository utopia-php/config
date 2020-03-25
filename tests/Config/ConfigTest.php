<?php
/**
 * Utopia PHP Framework
 *
 * @package Config
 * @subpackage Tests
 *
 * @link https://github.com/utopia-php/framework
 * @author Eldad Fux <eldad@appwrite.io>
 * @version 1.0 RC4
 * @license The MIT License (MIT) <http://www.opensource.org/licenses/mit-license.php>
 */

namespace Utopia\Tests;

use PHPUnit\Framework\TestCase;
use Utopia\Config\Config;

class ConfigTest extends TestCase
{
    public function setUp()
    {
        
    }

    public function tearDown()
    {
        $this->test = null;
    }

    public function testTest()
    {
        Config::setParam('key', 'value');
        $this->assertEquals('value', Config::getParam('key'));
        $this->assertEquals('default', Config::getParam('keyx', 'default'));
        
        Config::setParam('key', 'value2');
        $this->assertEquals('value2', Config::getParam('key'));
        
        Config::setParam('key2', 'value2');
        $this->assertEquals('value2', Config::getParam('key2'));
        
        Config::setParam('key3', ['key4' => 'value4']);
        $this->assertEquals(['key4' => 'value4'], Config::getParam('key3'));
        $this->assertEquals('value4', Config::getParam('key3.key4'));
        $this->assertEquals('default', Config::getParam('key3.keyx', 'default'));
        $this->assertEquals('default', Config::getParam('key3.key4.x', 'default'));
        
        Config::load('key5', __DIR__.'/demo.php');

        $this->assertEquals('value1', Config::getParam('key5.key1', 'default'));
        $this->assertEquals('value2', Config::getParam('key5.key2', 'default'));
        $this->assertEquals(['key1' => 'value1','key2' => 'value2',], Config::getParam('key5', 'default'));
        $this->assertEquals('default2', Config::getParam('key5.x', 'default2'));
    }
}