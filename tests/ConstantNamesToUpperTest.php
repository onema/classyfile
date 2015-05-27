<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\Test;
use Onema\ClassyFile\Event\ClassyFileEvent;
use Onema\ClassyFile\Event\GetClassEvent;
use Onema\ClassyFile\Plugin\ConstantNamesToUpper;
use PhpParser\Lexer;
use PhpParser\Parser;

/**
 * ConstantNamesToUpperTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema
 * @group constants
 */
class ConstantNamesToUpperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstantNameConversion()
    {
        $parser = new Parser(new Lexer());
        $class = file_get_contents(__DIR__.'/mock/constants/class_with_constants.php');
        $statement = $parser->parse($class);
        $event = new GetClassEvent($statement[0], '/tmp/');
        $event->setCode('<?php echo "some code";');
        $event->setUses('use Some\Namespace;');
        $event->setNamespace('namespace Current\Namespace;');

        $plugin = new ConstantNamesToUpper();
        $plugin->onSetClassUpdateConstants($event);

        $this->assertEquals('TWENTY', $event->getStatements()->stmts[0]->consts[0]->name);
        $this->assertEquals('TWENTY_ONE', $event->getStatements()->stmts[1]->consts[0]->name);
        $this->assertEquals('TWENTY_TWO', $event->getStatements()->stmts[2]->consts[0]->name);
        $this->assertEquals('TWENTY_THREE', $event->getStatements()->stmts[3]->consts[0]->name);
    }

    public function testSubscribedEvents()
    {
        $subscribedEvents = ConstantNamesToUpper::getSubscribedEvents();
        $this->assertArrayHasKey(ClassyFileEvent::GET_CLASS, $subscribedEvents, 'The subscriber is not returning a valid event.');
    }
}
