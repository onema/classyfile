<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Plugin;

use Onema\ClassyFile\Event\TraverseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * CreateNamespacePlugin - This plugin takes a section of the path where the file is located and sets it as the namespace.
 * For example, Assume that a file containing multiple classes with no namespace is located in /lib/Vendor/Client/v1/.
 * With this plugin we can take the section Vendor/Client and set it as the classes namespace.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class CreateNamespace implements EventSubscriberInterface
{
    /**
     * @var int
     */
    private $offset;
    /**
     * @var int
     */
    private $length;

    public function __construct($offset = 0, $length = 1)
    {
        $this->offset = $offset;
        $this->length = $length;
    }
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [TraverseEvent::BEFORE => ['onTraverseAddNamespace', 0]];
    }

    /**
     * Get a slice of the path and set it as the namespace.
     *
     * @param TraverseEvent $event
     */
    public function onTraverseAddNamespace(TraverseEvent $event)
    {
        $directoryPath = $event->getDirectoryPath();
        $namespaceArray = explode(DIRECTORY_SEPARATOR, $directoryPath);
        $arraySlice = array_slice($namespaceArray, $this->offset, $this->length);
        $namespace = implode('\\', $arraySlice);
        $event->setNamespace($namespace);
    }
}
