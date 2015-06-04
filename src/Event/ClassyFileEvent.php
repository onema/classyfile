<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * SetClassEvent - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class ClassyFileEvent extends Event
{
    /**
     * @var
     */
    private $statements;

    public function __construct($statements = null)
    {
        $this->statements = $statements;
    }
    /**
     * @return mixed $statement
     */
    public function getStatements()
    {
        return $this->statements;
    }

    public function setStatements($statements)
    {
        $this->statements = $statements;
    }
}
