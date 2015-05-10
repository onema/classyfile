<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile\Command;

use Onema\ClassyFile\ClassyFile;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateClassesFromFileCommand - Description.
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
                'path-to-directory',
                InputArgument::REQUIRED,
                'Path to the directory containing the PHP files.'
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filePath = $input->getArgument('path-to-directory');
        $createNamespace = $input->getOption('create-namespace');
        $offset = $input->getOption('offset');
        $length = $input->getOption('length');
        $classyfile = new ClassyFile();
        $classyfile->generateClassFiles($filePath, $createNamespace, $offset, $length);
    }
}
