<?php
/*
 * This file is part of the Onema {classyfile} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile\Plugin;

use Onema\ClassyFile\Event\GetClassEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Onema\ClassyFile\Event\ClassyFileEvent;
use Symfony\CS\Console\Command\FixCommand;

/**
 * PhpCsFixerPlugin - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class PhpCsFixer implements EventSubscriberInterface
{
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [ClassyFileEvent::AFTER_GET_CLASS => ['onGetClassFixClass', 5]];
    }

    public function onGetClassFixClass(GetClassEvent $event)
    {
        $fileLocation = $event->getFileLocation();
        $command = new FixCommand();

        $arguments = [
            'path'    => $fileLocation,
        ];

        $output = new BufferedOutput();
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }
}
