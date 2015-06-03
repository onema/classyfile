<?php
/*
 * This file is part of the CPCStrategy {classyfile} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\Test;

use Onema\ClassyFile\Event\GetClassEvent;
use Onema\ClassyFile\Plugin\PhpCsFixer;
use PhpParser\Node\Stmt\Class_;

/**
 * PhpCsFixerPluginTest - Description. 
 *
 * @author Juan Manuel Torres <juan@cpcstrategy.com>
 * @copyright (c) 2015, CPC Strategy Development Team
 * @group fixer
 */
class PhpCsFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testOnGetClassFixClass ()
    {
        $pathToFile = __DIR__.'/mock/fixer/original/GetSomeResponse.php';
        $pathToFileFixed = __DIR__.'/mock/fixer/fixed/GetSomeResponse.php';
        $code = file_get_contents($pathToFile);
        $statement = new Class_($code);
        $event = new GetClassEvent($statement, $pathToFile);

        $event->setCode($code);
        $event->setUses('use DateTime;');
        $event->setNamespace('namespace SomeNameSpace\Category;');

        $plugin = new PhpCsFixer();
        $plugin->onGetClassFixClass($event);

        $code = file_get_contents($pathToFile);
        $fixedCode = file_get_contents($pathToFileFixed);
        $this->assertEquals($fixedCode, $code, 'Files are not equal and they should be.');
    }
}
