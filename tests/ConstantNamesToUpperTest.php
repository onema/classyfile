<?php
/*
 * This file is part of the Onema {test} Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\Test;
use Onema\ClassyFile\ClassyFileEvent;
use Onema\ClassyFile\Plugin\ConstantNamesToUpper;
use PhpParser\Lexer;
use PhpParser\Parser;

/**
 * ConstantNamesToUpperTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema
 */
class ConstantNamesToUpperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstantNameConversion()
    {
        $event = new ClassyFileEvent();
        $event['code'] = 'some code';
        $event['file_location'] = '/tmp/';
        $parser = new Parser(new Lexer());
        $class = file_get_contents(__DIR__.'/mock/constants/class_with_constants.php');
        $statement = $parser->parse($class);
        $event->setStatement($statement[0]);
        $plugin = new ConstantNamesToUpper();
        $plugin->onSetClassUpdateConstants($event);

        $this->assertEquals('TWENTY', $event['statement']->stmts[0]->consts[0]->name);
        $this->assertEquals('TWENTY_ONE', $event['statement']->stmts[1]->consts[0]->name);
        $this->assertEquals('TWENTY_TWO', $event['statement']->stmts[2]->consts[0]->name);
        $this->assertEquals('TWENTY_THREE', $event['statement']->stmts[3]->consts[0]->name);
    }
}
