<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Plugin;

use League\Flysystem\Adapter\AbstractAdapter;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\Util;
use Onema\ClassyFile\Event\GetClassEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * GenerateClassFile - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class GenerateClassFile implements EventSubscriberInterface
{
    /**
     * @var \League\Flysystem\FilesystemInterface
     */
    private $filesystem;

    public function __construct(AdapterInterface $adapter)
    {
        $this->filesystem = new Filesystem($adapter);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [GetClassEvent::AFTER => ['onGetClassGenerateFile', 10]];
    }

    /**
     * Use flysystem to save the file in the desired location.
     *
     * @param \Onema\ClassyFile\Event\GetClassEvent $event
     */
    public function onGetClassGenerateFile(GetClassEvent $event)
    {
        $statement = $event->getStatements();
        $fileLocation = $event->getFileLocation();
        $code = $event->getCode();
        $name = $statement->name;

        if (!$this->filesystem->has($fileLocation)) {
            // dir doesn't exist, make it
            $this->filesystem->createDir($fileLocation);
        }

        $location = sprintf('%s/%s.php', $fileLocation, $name);
        $this->filesystem->put($location, $code);
        $adapter = $this->filesystem->getAdapter();

        if ($adapter instanceof AbstractAdapter) {
            $prefix = $adapter->getPathPrefix();
            $location = Util::normalizePath($location);
            $event->setFileLocation(sprintf('%s%s', $prefix, $location));
        }
    }
}
