<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Plugin;

use Onema\ClassyFile\Event\GetClassEvent;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\CS\Console\Command\FixCommand;

/**
 * PhpCsFixerPlugin - A plugin that provides a wrapper around the PHP CS Fixer. It will run the fixer on a class
 * that has been generated and saved as a file. It will depend on GenerateClassFile or similar plugin that provies
 * the location of the newly generated class.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class PhpCsFixer implements EventSubscriberInterface
{
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var array
     */
    private $arguments = [];

    public function __construct(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->output = $output;
        $this->input = $input;

        if (isset($this->input)) {
            $this->createDefaultFixerArguments();
        }
    }
    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [GetClassEvent::AFTER => ['onGetClassFixClass', 5]];
    }

    public function onGetClassFixClass(GetClassEvent $event)
    {
        $fileLocation = $event->getFileLocation();
        $command = new FixCommand();

        $arguments = [
            'path' => $fileLocation,
        ];

        $arguments = array_merge($arguments, $this->arguments);

        $output = isset($this->output) ? $this->output : new BufferedOutput();
        $output->writeln(sprintf('<info>Running PSR Fixer on %s</info>', $fileLocation));
        $input = new ArrayInput($arguments);
        $command->run($input, $output);
    }

    public function getArguments()
    {
        return $this->arguments;
    }

    protected function createDefaultFixerArguments()
    {
        if ($this->input->getOption('verbose')) {
            $this->arguments[] = '--verbose';
        }
    }
}
