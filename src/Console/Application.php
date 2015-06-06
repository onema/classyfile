<?php
/*
 * This file is part of the Onema {classyfile} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */

namespace Onema\ClassyFile\Console;

use Onema\ClassyFile\ClassyFile;
use Onema\ClassyFile\Console\Command\GenerateClassesFromFileCommand;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Application - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class Application extends BaseApplication
{
    public function __construct()
    {
        error_reporting(-1);
        $title = <<<EOT
  _____ __    ___    ____ ______  __ ____ ____ __    ____
 / ___// /   / _ |  / __// __/\ \/ // __//  _// /   / __/
/ /__ / /__ / __ | _\ \ _\ \   \  // _/ _/ / / /__ / _/
\___//____//_/ |_|/___//___/   /_//_/  /___//____//___/


EOT;

        parent::__construct($title, ClassyFile::VERSION);

        $this->add(new GenerateClassesFromFileCommand());
    }

    public function getLongVersion()
    {
        $version = parent::getLongVersion().' by <comment>Juan Manuel Torres</comment>';
        return $version;
    }
}
