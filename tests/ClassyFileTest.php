<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\Test;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Onema\ClassyFile\ClassyFile;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * ClassyFileTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com.com>
 * @copyright (c) 2015, Onema
 * @group classyfile
 */
class ClassyFileTest extends \PHPUnit_Framework_TestCase
{
    public function testClassToFileConversionStyle1()
    {
        // Delete all files
        $this->deleteFiles('/tmp/', '/Service/');

        $classyfile = new ClassyFile();
        $dispatcher = new EventDispatcher();
        $classyfile->setEventDispatcher($dispatcher);

        $codeLocation = __DIR__.'/mock/';
        $codeDestination = '/tmp/';
        $classyfile->generateClassFiles($codeDestination, $codeLocation);

        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/Scale.php', 'File was not created in the right location.');
        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/ServiceSettings.php', 'File was not created in the right location.');
        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/TimeInterval.php', 'File was not created in the right location.');
        $this->assertEquals($dispatcher, $classyfile->getEventDispatcher());

        // Delete all files
        $this->deleteFiles($codeDestination, '/Service/');
    }

    public function testClassToFileConversionMocked()
    {
        $mockAdapter = $this->getMockBuilder('\League\Flysystem\Adapter\Local')
            ->disableOriginalConstructor()
            ->setMethods(['has', 'createDir', 'write'])
            ->getMock();

        // plugin calls this method 3 times, internally write calls this method each time checking if
        // the file exist. write has must always return false otherwise it will throw an exception.
        $mockAdapter->expects($this->exactly(6))
            ->method('has')
            ->will($this->onConsecutiveCalls(false, false, true, false, true, false));

        $mockAdapter->expects($this->once())
            ->method('createDir')
            ->willReturn(true);

        $mockAdapter->expects($this->exactly(3))
            ->method('write')
            ->willReturn(true);

        $classyfile = new ClassyFile();
        $classyfile->setFilesystemAdapter($mockAdapter);
        $dispatcher = new EventDispatcher();
        $classyfile->setEventDispatcher($dispatcher);

        $codeLocation = __DIR__.'/mock/';
        $codeDestination = '/tmp/';
        $classyfile->generateClassFiles($codeDestination, $codeLocation);
    }

    public function testClassToFileConversionStyle2()
    {
        $classyfile = new ClassyFile();

        $dispatcher = new EventDispatcher();
        $classyfile->setEventDispatcher($dispatcher);

        $codeLocation = __DIR__.'/mock/src/VendorName/ProjectName/Category/ProductName/v123456/';
        $codeLocationArray = explode('/', $codeLocation);
        $codeLocationArray = array_splice($codeLocationArray, 6, 4);
        $directory = $codeLocationArray[0];
        $destinationLocation = implode('/', $codeLocationArray);
        $codeDestination = '/tmp/';

        // Delete all files/
        $this->deleteFiles($codeDestination, sprintf('/%s/', $directory));

        $classyfile->generateClassFiles($codeDestination, $codeLocation, 6, 4);

        $this->assertFileExists(sprintf('/%s/%s/Date.php', $codeDestination, $destinationLocation));
        $this->assertFileExists(sprintf('/%s/%s/DateRange.php', $codeDestination, $destinationLocation));
        $this->assertFileExists(sprintf('/%s/%s/OrderBy.php', $codeDestination, $destinationLocation));

        // Delete all files/
        $this->deleteFiles($codeDestination, sprintf('/%s/', $directory));
    }

    public function testClassToFileConversionStyle2NoNamespaces()
    {
        // Delete all files/
        $this->deleteFiles('/tmp/', '/tmp/');

        $classyfile = new ClassyFile();

        $dispatcher = new EventDispatcher();
        $classyfile->setEventDispatcher($dispatcher);

        $codeLocation = __DIR__.'/mock/src/VendorName/ProjectName/Category/ProductName/v123456/';
        $codeDestination = '/tmp/';
        $classyfile->generateClassFiles($codeDestination, $codeLocation);

        $this->assertFileExists('/tmp/tmp/Date.php');
        $this->assertFileExists('/tmp/tmp/DateRange.php');
        $this->assertFileExists('/tmp/tmp/OrderBy.php');

        // Delete all files/
        $this->deleteFiles($codeDestination, '/tmp/');
    }

    public function testNewTemplate()
    {
        $codeLocation = __DIR__.'/mock/';
        $classyfile = new ClassyFile();
        $i = 0;
        $classyfile->setTemplate(function ($namespace, $uses, $comments, $code) use (&$i) {
            if ($i == 0) {
                $i++;
                return $namespace; // returns namespace Service\WithBad\ClassFiles;
            } elseif ($i == 1) {
                $i++;
                return $comments;  // returns /** comment */
            } elseif ($i == 2) {
                $i++;
                return $uses;      // returns use \DateTime;
            }
        });
        $code = $classyfile->generateClasses($codeLocation);
        $this->assertEquals('namespace Service\WithBad\ClassFiles;', $code['mock_classes_style1.php']['ServiceSettings']);
        $this->assertEquals('/** comment */', $code['mock_classes_style1.php']['TimeInterval']);
        $this->assertEquals("use DateTime;\n", $code['mock_classes_style1.php']['Scale']);
    }

    public function testErrorException()
    {
        $this->setExpectedException('\Onema\ClassyFile\Exception\ClassToFileRuntimeException');
        $classyfile = new ClassyFile();

        $codeLocation = __DIR__.'/mock/bad/';
        $classyfile->generateClasses($codeLocation);
    }

    private function deleteFiles($codeDestination, $directory)
    {
        // Delete all files/
        $filesystem = new Filesystem(new Local($codeDestination));
        $filesystem->deleteDir($directory);
    }
}
