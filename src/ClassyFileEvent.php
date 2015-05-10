<?php
/*
 * This file is part of the Onema {test} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile;

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
    const SET_CLASS = 'classyfile.set_class';

    /**
     * @return mixed
     */
    public function getStatement()
    {
        return $this->getSubject();
    }
}
