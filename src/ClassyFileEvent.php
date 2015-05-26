<?php
/*
 * This file is part of the Onema {test} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile;

use PhpParser\Node\Stmt;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * SetClassEvent - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class ClassyFileEvent extends GenericEvent
{
    const TRAVERSE = 'classyfile.traverse';
    const GET_CLASS = 'classyfile.get_class';
    const AFTER_GET_CLASS = 'classyfile.after_get_class';

    /**
     * @return \PhpParser\Node\Stmt $statement
     */
    public function getStatement()
    {
        return $this->getArgument('statement');
    }

    public function setStatement(Stmt $statement)
    {
        $this->setArgument('statement', $statement);
    }
}
