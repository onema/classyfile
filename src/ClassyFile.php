<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Onema\ClassyFile\Event\ClassyFileEvent;
use Onema\ClassyFile\Event\GetClassEvent;
use Onema\ClassyFile\Event\TraverseEvent;
use Onema\ClassyFile\Plugin\CreateNamespace;
use Onema\ClassyFile\Plugin\GenerateClassFile;
use Onema\ClassyFile\Exception\ClassToFileRuntimeException;
use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * ClassyFile - Description.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, Onema
 */
class ClassyFile
{
    /**
     * @var \PhpParser\PrettyPrinter\Standard $prettyPrinter
     */
    protected $prettyPrinter;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var \League\Flysystem\AdapterInterface
     */
    protected $filesystemAdapter;

    /**
     * @var callable
     */
    private $template;

    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        if (!isset($dispatcher)) {
            $this->dispatcher = new EventDispatcher();
        } else {
            $this->dispatcher = $dispatcher;
        }

        $this->prettyPrinter = new Standard();
        $this->setTemplate(function ($namespace, $uses, $comments, $code) {
            return '<?php'.
            PHP_EOL.PHP_EOL.
            $namespace .
            PHP_EOL.
            $uses.
            PHP_EOL.
            $comments.
            PHP_EOL.
            $code.
            PHP_EOL;});
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher)
    {
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param \League\Flysystem\AdapterInterface
     */
    public function setFilesystemAdapter(AdapterInterface $filesystemAdapter)
    {
        $this->filesystemAdapter = $filesystemAdapter;
    }

    /**
     * This method is just a wrapper to the generateClasses method. It will add an event subscriber that will
     * save the file to the desired location. The file uses a local file system addapter, but any file system may be
     * used, this way files can be saved to remote locations.
     *
     * @param $codeDestination
     * @param $codeLocation
     * @param int $offset
     * @param int $length
     */
    public function generateClassFiles($codeDestination, $codeLocation, $offset = 0, $length = 0)
    {
        // add default local file system adapter.
        if (!isset($this->filesystemAdapter)) {
            $this->filesystemAdapter = new Local($codeDestination);
        }

        if ($length !== 0) {
            $this->dispatcher->addSubscriber(new CreateNamespace($offset, $length));
        }

        $filesystem = new Filesystem($this->filesystemAdapter);
        $this->dispatcher->addSubscriber(new GenerateClassFile($filesystem));
        $this->generateClasses($codeLocation);
    }

    /**
     * @param $directoryPath
     * @returns array $code
     */
    public function generateClasses($directoryPath)
    {
        // Get all the files in the given directory
        $finder = new Finder();
        $finder->depth('== 0')->files()->in(rtrim($directoryPath, '/'))->name('*.php');
        $parser = new Parser(new Lexer());
        $code = [];
        $namespace = '';

        foreach ($finder as $file) {
            if ($file instanceof SplFileInfo) {

                $classes = $file->getContents();

                try {
                    $statements = $parser->parse($classes);

                    $event = new TraverseEvent($statements, $namespace, $directoryPath, $file->getFilename());
                    $this->dispatcher->dispatch(ClassyFileEvent::TRAVERSE, $event);
                    $statements = $event->getStatements();
                    $namespace = $event->getNamespace();
                    $filename = $event->getFile();

                    $code[$filename] = $this->traverseStatements($statements, $namespace);

                } catch (Error $e) {
                    throw new ClassToFileRuntimeException(sprintf('Parse Error: %s', $e->getMessage()));
                }
            }
        }

        return $code;
    }

    /**
     * @param $statements
     * @param string $namespaceString
     * @returns array $code
     */
    protected function traverseStatements($statements, $namespaceString = '')
    {
        $uses = '';
        $code = [];

        foreach ($statements as $statement) {

            if ($statement instanceof Namespace_) {

                $namespaceString = implode('\\', $statement->name->parts);
                $nestedCode = $this->traverseStatements($statement->stmts, $namespaceString);
                $code = array_merge($code, $nestedCode);

            } elseif ($statement instanceof Class_) {

                if (empty($namespaceString)) {
                    $namespaceString = 'tmp';
                }

                $fileLocation = DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespaceString);
                $code[$statement->name] = $this->getClass($statement, $namespaceString, $fileLocation, $uses);

            } elseif ($statement instanceof If_) {

                $nestedCode = $this->traverseStatements($statement->stmts, $namespaceString);
                $code = array_merge($code, $nestedCode);

            } elseif ($statement instanceof Use_) {

                $uses .= $this->prettyPrinter->pStmt_Use($statement).PHP_EOL;
            }
        }

        return $code;
    }

    /**
     * @param Class_ $statement
     * @param string $namespace
     * @param string $fileLocation
     * @param string $uses
     * @returns string $code
     */
    protected function getClass(Class_ $statement, $namespace, $fileLocation, $uses = '')
    {
        $comments = $statement->getDocComment();
        $comments = isset($comments) ? $comments->getText() : '';

        if ($namespace == 'tmp') {
            $namespace = '';
        } else {
            $namespace = 'namespace '.$namespace.';';
        }

        $event = new GetClassEvent($statement, $fileLocation, $namespace, $uses);
        $this->dispatcher->dispatch(ClassyFileEvent::GET_CLASS, $event);
        $namespace = $event->getNamespace();
        $uses = $event->getUses();
        $statement = $event->getStatements();

        $code = $this->prettyPrinter->pStmt_Class($statement);

        if (isset($this->template)) {
            $code = call_user_func($this->template, $namespace, $uses, $comments, $code);
        }

        $event->setCode($code);
        $this->dispatcher->dispatch(ClassyFileEvent::AFTER_GET_CLASS, $event);
        $code = $event->getCode();

        return $code;
    }

    /**
     * @param callable $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
