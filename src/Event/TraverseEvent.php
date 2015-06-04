<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Event;

/**
 * GetClassEvent - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class TraverseEvent extends ClassyFileEvent
{
    const BEFORE = 'classyfile.before_traverse';

    /**
     * @var
     */
    private $namespace;
    /**
     * @var
     */
    private $file;
    /**
     * @var
     */
    private $directoryPath;

    public function __construct($statements, $namespace, $directoryPath, $file)
    {
        parent::__construct($statements);
        $this->namespace = $namespace;
        $this->file = $file;
        $this->directoryPath = $directoryPath;
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getDirectoryPath()
    {
        return $this->directoryPath;
    }

    /**
     * @param mixed $directoryPath
     */
    public function setDirectoryPath($directoryPath)
    {
        $this->directoryPath = $directoryPath;
    }
}
