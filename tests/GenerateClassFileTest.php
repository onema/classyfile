<?php
/*
 * This file is part of the Onema {test} Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\Test;

use League\Flysystem\Filesystem;
use Onema\ClassyFile\ClassyFileEvent;
use Onema\ClassyFile\Plugin\GenerateClassFile;
use PhpParser\Node\Stmt\Class_;

/**
 * GenerateClassFileTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, Onema
 * @group plugin
 */
class GenerateClassFileTest extends \PHPUnit_Framework_TestCase
{
    public function testSavesFile()
    {
        $mockAdapter = $this->getMockBuilder('\League\Flysystem\Adapter\Local')
            ->disableOriginalConstructor()
            ->setMethods(['has', 'createDir', 'write'])
            ->getMock();

        // plugin calls this method 3 times, internally write calls this method each time checking if
        // the file exist. write has must always return false otherwise it will throw an exception.
        $mockAdapter->expects($this->exactly(2))
            ->method('has')
            ->will($this->onConsecutiveCalls(false, false));

        $mockAdapter->expects($this->once())
            ->method('createDir')
            ->willReturn(true);

        $mockAdapter->expects($this->exactly(1))
            ->method('write')
            ->willReturn(true);

        $event = new ClassyFileEvent();
        $event['code'] = 'some code';
        $event['file_location'] = '/tmp/';
        $statement = new Class_('some_name');
        $event->setStatement($statement);

        $plugin = new GenerateClassFile(new Filesystem($mockAdapter));

        $plugin->onGetClassGenerateFile($event);
    }
}
