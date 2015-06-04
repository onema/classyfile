<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\Test;

use Onema\ClassyFile\Event\ClassyFileEvent;
use Onema\ClassyFile\Event\TraverseEvent;
use Onema\ClassyFile\Plugin\CreateNamespace;

/**
 * CreateNamespaceTest - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, Onema
 * @group namespace
 */
class CreateNamespaceTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNamespace()
    {
        $statement = new \stdClass();
        $event = new TraverseEvent($statement, '', '/foo/Vendor/Product/src/', 'file.php');
        $plugin = new CreateNamespace(2, 2);
        $plugin->onTraverseAddNamespace($event);
        $namespace = $event->getNamespace();

        $this->assertEquals('Vendor\Product', $namespace);

        $event->setDirectoryPath('/lala/foo/bar/poz/');
        $event->setFile('FooBar.php');
        $plugin->onTraverseAddNamespace($event);
        $namespace = $event->getNamespace();

        $this->assertEquals('foo\bar', $namespace);
    }

    public function testSubscribedEvents()
    {
        $subscribedEvents = CreateNamespace::getSubscribedEvents();
        $this->assertArrayHasKey(TraverseEvent::BEFORE, $subscribedEvents, 'The subscriber is not returning a valid event.');
    }
}
