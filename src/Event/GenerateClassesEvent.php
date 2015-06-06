<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */

namespace Onema\ClassyFile\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Finder\Finder;

/**
 * GenerateClassesEvent - Event used in the "generate class" process.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class GenerateClassesEvent extends Event
{
    const BEFORE = 'classyfile.before_generate_classes';
    const AFTER = 'classyfile.after_generate_classes';

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    private $finder;
    /**
     * @var array
     */
    private $classes;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return \Symfony\Component\Finder\Finder
     */
    public function getFinder()
    {
        return $this->finder;
    }

    /**
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }

    /**
     * @param array $classes
     */
    public function setClasses(array $classes)
    {
        $this->classes = $classes;
    }
}
