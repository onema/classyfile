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
use Onema\ClassyFile\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * ApplicationTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com.com>
 * @copyright (c) 2015, Onema
 *
 * @group application
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    public function testIfVersionNumberIsCorrect()
    {
        if (!class_exists('Symfony\Component\Console\Output\BufferedOutput')) {
            $this->markTestSkipped('Unsupported symfony/console version, Symfony\Component\Console\Output\BufferedOutput was added in 2.4.');
        }

        $input = new ArrayInput(['--version']);
        $output = new BufferedOutput();
        $app = new Application();

        $app->doRun($input, $output);

        $this->assertRegExp(
            sprintf('/%s/', ClassyFile::VERSION),
            $output->fetch(),
            'The application version does not match the ClassyFile version.'
        );
    }

    public function testConvertCommand()
    {
        if (!class_exists('Symfony\Component\Console\Output\BufferedOutput')) {
            $this->markTestSkipped('Unsupported symfony/console version, Symfony\Component\Console\Output\BufferedOutput was added in 2.4.');
        }

        $filesystem = new Filesystem(new Local('/tmp'));

        $input = new ArrayInput([
            'convert',
            'code-location' => __DIR__.'/mock/',
            '--code-destination' => '/tmp',
            '--constants-to-upper' => true,
            '--psr-fix' => true,
            '--remove-top-comment' => true,
        ]);
        $output = new BufferedOutput();
        $app = new Application();

        $app->get('convert')->run($input, $output);

        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/ServiceSettings.php');
        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/TimeInterval.php');
        $this->assertFileExists('/tmp/Service/WithBad/ClassFiles/Scale.php');

        $filesystem->deleteDir('/Service/');
    }

    public function testConvertCommandWithNamespaceOption()
    {
        $codeLocation = __DIR__.'/mock/src/VendorName/ProjectName/Category/ProductName/v123456/';
        $codeLocationArray = explode('/', $codeLocation);
        $codeLocationArray = array_splice($codeLocationArray, 6, 4);
        $directory = $codeLocationArray[0];
        $destinationLocation = implode('/', $codeLocationArray);
        $codeDestination = '/tmp/';

        $input = new ArrayInput([
            'convert',
            'code-location' => $codeLocation,
            '--code-destination' => '/tmp',
            '--create-namespace' => true,
            '--offset' => 6,
            '--length' => 4,
        ]);
        $output = new BufferedOutput();
        $app = new Application();

        $app->get('convert')->run($input, $output);

        $this->assertFileExists(sprintf('/%s/%s/Date.php', $codeDestination, $destinationLocation));
        $this->assertFileExists(sprintf('/%s/%s/DateRange.php', $codeDestination, $destinationLocation));
        $this->assertFileExists(sprintf('/%s/%s/OrderBy.php', $codeDestination, $destinationLocation));

        // Delete all files/
        $filesystem = new Filesystem(new Local($codeDestination));
        $filesystem->deleteDir($directory);
    }
}
