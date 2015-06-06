<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Console\Command;

use Onema\ClassyFile\ClassyFile;
use Onema\ClassyFile\Plugin\ConstantNamesToUpper;
use Onema\ClassyFile\Plugin\PhpCsFixer;
use Onema\ClassyFile\Template\BasicClassTemplate;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * GenerateClassesFromFileCommand - Command to run classyfile.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, Onema
 */
class GenerateClassesFromFileCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('convert')
            ->setDescription('Opens a directory and reads every PHP file, it takes the classes in the file and generates one file per class.')
            ->addArgument(
                'code-location',
                InputArgument::REQUIRED,
                'Path to the directory containing the PHP files that will be converted.'
            )
            ->addOption(
                'code-destination',
                'd',
                InputOption::VALUE_REQUIRED,
                'Path to the directory where the new generated files will be saved. Default CWD.',
                getcwd()
            )
            ->addOption(
                'create-namespace',
                null,
                InputOption::VALUE_NONE,
                'Name spaces are created by default, if the file does not include namespaces one can be created using a section of the path. Use offset and length to identify which section of the path to use.'
            )
            ->addOption(
                'offset',
                null,
                InputOption::VALUE_OPTIONAL,
                'Offset is used if the create namespace is enabled, it gets a section of the path to directory to use as a namespace starting at the given offset.',
                0
            )
            ->addOption(
                'length',
                null,
                InputOption::VALUE_OPTIONAL,
                'Length is used if the create namespace is enabled, it determines how many sections of the path to use starting at the given offset.',
                1
            )
            ->addOption(
                'constants-to-upper',
                null,
                InputOption::VALUE_NONE,
                'Adds a plugin to convert constant names to uppercase e.g. constantName to CONSTANT_NAME.'
            )
            ->addOption(
                'psr-fix',
                null,
                InputOption::VALUE_NONE,
                'Run all PHP CS Fixers. This will fix a lot of formatting issues.'
            )
            ->addOption(
                'remove-top-comment',
                null,
                InputOption::VALUE_NONE,
                'Removes the top file comment from all files.'
            )
            ->setHelp(<<<EOT
The <info>%command.name%</info> command is used to refactor classes that have been placed in a single file along
with other classes (multiple classes per file). The command will open each file within a single directory
(non recursively) and generate one file per class and save each file in the "code-destination". Several options
are available to help refactoring these classes and to make them PSR-1 and PSR-2.

<info>php classyfile %command.name%</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $codeLocation = $input->getArgument('code-location');
        $codeDestination = $input->getOption('code-destination');
        $createNamespace = $input->getOption('create-namespace');
        $offset = $input->getOption('offset');
        $length = $input->getOption('length');
        $classyfile = new ClassyFile();
        $dispatcher = new EventDispatcher();

        if ($input->getOption('constants-to-upper')) {
            $plugin = new ConstantNamesToUpper();
            $dispatcher->addSubscriber($plugin);
        }

        if ($input->getOption('psr-fix')) {
            $plugin = new PhpCsFixer($input, $output);
            $dispatcher->addSubscriber($plugin);
        }

        if ($input->getOption('removet-top-comment')) {
            $classyfile->setTemplate(new BasicClassTemplate(''), 'getTemplate');
        }

        $classyfile->setEventDispatcher($dispatcher);

        if ($createNamespace) {
            $classyfile->generateClassFiles($codeDestination, $codeLocation, $offset, $length);
        } else {
            $classyfile->generateClassFiles($codeDestination, $codeLocation);
        }
    }
}
