<?php
/*
 * This file is part of the Onema {test} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile\Plugin;

use League\Flysystem\FilesystemInterface;
use Onema\ClassyFile\ClassyFileEvent;
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

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [ClassyFileEvent::AFTER_GET_CLASS => 'onGetClassGenerateFile'];
    }

    /**
     * Use flysystem to save the file in the desired location.
     *
     * @param ClassyFileEvent $event
     */
    public function onGetClassGenerateFile(ClassyFileEvent $event)
    {
        $statement = $event->getStatement();
        $fileLocation = $event->getArgument('file_location');
        $code = $event->getArgument('code');
        $name = $statement->name;

        if (!$this->filesystem->has($fileLocation)) {
            // dir doesn't exist, make it
            $this->filesystem->createDir($fileLocation);
        }

        $location = sprintf('%s/%s.php', $fileLocation, $name);
        $this->filesystem->write($location, $code);
    }
}
