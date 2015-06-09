<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */

namespace Onema\ClassyFile\Template;

/**
 * BasicClassTemplate - Class to generate a basic template.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class BasicClassTemplate
{
    /**
     * @var string
     */
    private $topComment;

    public function __construct ($topComment = null)
    {
        if (!isset($topComment)) {
            $date = new \DateTime();
            $this->topComment = <<<EOT

/*
 * File generated by ClassyFile <https://github.com/onema/classyfile>
 * (c) {$date->format('Y')}, Juan Manuel Torres
 *
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

EOT;
        } else {
            $this->topComment = $topComment;
        }
    }

    /**
     * Return a string defining a php class using the parameters defined below.
     *
     * @param $namespace
     * @param $useStatements
     * @param $comments
     * @param $code
     * @return string
     */
    public function getTemplate ($namespace, $useStatements, $comments, $code)
    {
        return '<?php'.
        $this->topComment.
        $namespace . PHP_EOL.
        $useStatements . PHP_EOL.
        $comments . PHP_EOL.
        $code . PHP_EOL;
    }
}
