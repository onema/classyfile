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
use Onema\ClassyFile\Exception\MissingEventDispatcherException;
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

    public function __construct()
    {
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
     * @param bool $createNamespace
     * @param int $offset
     * @param int $length
     */
    public function generateClassFiles($codeDestination, $codeLocation, $createNamespace = false, $offset = 0, $length = 1)
    {
        if (!isset($this->dispatcher)) {
            throw new MissingEventDispatcherException('This operation requires an instance of "Symfony\Component\EventDispatcher\EventDispatcherInterface".');
        }

        // add default local file system adapter.
        if (!isset($this->filesystemAdapter)) {
            $this->filesystemAdapter = new Local($codeDestination);
        }

        $filesystem = new Filesystem($this->filesystemAdapter);
        $this->dispatcher->addSubscriber(new GenerateClassFile($filesystem));
        $this->generateClasses($codeLocation, $createNamespace, $offset, $length);
    }

    /**
     * @param $directoryPath
     * @param bool $createNamespace
     * @param int $offset
     * @param int $length
     * @returns array $code
     */
    public function generateClasses($directoryPath, $createNamespace = false, $offset = 0, $length = 1)
    {
        // Get all the files in the given directory
        $files = array_diff(scandir($directoryPath), array('..', '.'));
        $parser = new Parser(new Lexer());
        $code = [];

        foreach ($files as $file) {
            $classes = file_get_contents(sprintf('%s/%s', $directoryPath, $file));
            $parts = pathinfo($file);
            if (isset($parts['extension']) && $parts['extension'] === 'php') {
                try {

                    $statements = $parser->parse($classes);

                    if (!empty($createNamespace)) {
                        $namespaceArray = explode(DIRECTORY_SEPARATOR, $directoryPath);
                        $arraySlice = array_slice($namespaceArray, $offset, $length);
                        $namespace = implode('\\', $arraySlice);
                    } else {
                        $namespace = '';
                    }

                    if ($this->dispatcher) {
                        $event = $this->dispatch(ClassyFileEvent::TRAVERSE, [
                            'statements' => $statements,
                            'create_namespace' => $createNamespace,
                            'offset' => $offset,
                            'length' => $length
                        ]);
                        $statements = $event->getArgument('statements');
                        $createNamespace = $event->getArgument('create_namespace');
                        $offset = $event->getArgument('offset');
                        $length = $event->getArgument('length');
                    }

                    $code[$file] = $this->traverseStatements($statements, $namespace);

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

        if ($this->dispatcher) {
            $event = $this->dispatch(ClassyFileEvent::GET_CLASS, [
                'statement' => $statement,
                'namespace' => $namespace,
                'file_location' => $fileLocation,
                'uses' => $uses
            ]);
            $namespace = $event->getArgument('namespace');
            $uses = $event->getArgument('uses');
            $statement = $event->getStatement();
        }

        $code = $this->prettyPrinter->pStmt_Class($statement);

        if (isset($this->template)) {
            $code = call_user_func($this->template, $namespace, $uses, $comments, $code);
        }

        if ($this->dispatcher) {
            $event = $this->dispatch(ClassyFileEvent::AFTER_GET_CLASS, [
                'code' => $code,
                'statement' => $statement,
                'file_location' => $fileLocation,
            ]);
            $code = $event->getArgument('code');
        }

        return $code;
    }

    protected function dispatch($eventName, array $arguments = [])
    {
        $event = new ClassyFileEvent(null, $arguments);
        $this->dispatcher->dispatch($eventName, $event);
        return $event;
    }

    /**
     * @param callable $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}
