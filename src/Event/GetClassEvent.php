<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Event;

use PhpParser\Node\Stmt;

/**
 * GetClassEvent - Event used in the "get class" process.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class GetClassEvent extends ClassyFileEvent
{
    const BEFORE = 'classyfile.before_get_class';
    const AFTER = 'classyfile.after_get_class';
    /**
     * @var mixed
     */
    private $namespace;
    /**
     * @var mixed
     */
    private $uses;
    /**
     * @var mixed
     */
    private $code;
    /**
     * @var null
     */
    private $fileLocation;

    public function __construct(Stmt $statement, $fileLocation = null, $namespace = null, $uses = null, $code = null)
    {
        parent::__construct($statement);
        $this->namespace = $namespace;
        $this->uses = $uses;
        $this->code = $code;
        $this->fileLocation = $fileLocation;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getUses()
    {
        return $this->uses;
    }

    /**
     * @param mixed $uses
     */
    public function setUses($uses)
    {
        $this->uses = $uses;
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
     */
    public function getFileLocation()
    {
        return $this->fileLocation;
    }

    /**
     * @param null $fileLocation
     */
    public function setFileLocation($fileLocation)
    {
        $this->fileLocation = $fileLocation;
    }
}
