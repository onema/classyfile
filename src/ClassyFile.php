<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile;

use Oneme\ClassyFile\Exception\ClassToFileRuntimeException;
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

    public function __construct()
    {
        $this->prettyPrinter = new Standard();
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
     * @param $directoryPath
     * @param bool $createNamespace
     * @param int $offset
     * @param int $length
     */
    public function generateClassFiles($directoryPath, $createNamespace = false, $offset = 0, $length = 1)
    {
        // Get all the files in the given directory
        $files = array_diff(scandir($directoryPath), array('..', '.'));
        $parser = new Parser(new Lexer());

        foreach ($files as $file) {
            $classes = file_get_contents(sprintf('%s/%s', $directoryPath, $file));
            $parts = pathinfo($file);
            if ($parts['extension'] === 'php') {
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

                    $this->traverseStatements($statements, $namespace);

                } catch (Error $e) {
                    throw new ClassToFileRuntimeException(sprintf('Parse Error: %s', $e->getMessage()));
                }
            }
        }
    }

    /**
     * @param $statements
     * @param string $namespaceString
     */
    protected function traverseStatements($statements, $namespaceString = '')
    {
        $uses = '';

        foreach ($statements as $statement) {

            if ($statement instanceof Namespace_) {

                $namespaceString = implode('\\', $statement->name->parts);
                $this->traverseStatements($statement->stmts, $namespaceString);

            } elseif ($statement instanceof Class_) {

                if (empty($namespaceString)) {
                    $namespaceString = 'tmp';
                }

                $fileLocation = getcwd() . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $namespaceString);
                $this->setClass($statement, $namespaceString, $fileLocation, $uses);

            } elseif ($statement instanceof If_) {

                $this->traverseStatements($statement->stmts, $namespaceString);

            } elseif ($statement instanceof Use_) {

                $uses .= $this->prettyPrinter->pStmt_Use($statement).PHP_EOL;
            }
        }
    }

    /**
     * @param Class_ $statement
     * @param string $namespace
     * @param string $fileLocation
     * @param string $uses
     */
    protected function setClass(Class_ $statement, $namespace, $fileLocation, $uses = '')
    {
        $comments = $statement->getDocComment();
        $comments = isset($comments) ? $comments->getText() : '';

        if ($namespace == 'tmp') {
            $namespace = '';
        } else {
            $namespace = 'namespace '.$namespace.';';
        }

        if ($this->dispatcher) {
            $event = $this->dispatch(ClassyFileEvent::SET_CLASS, [
                'statement' => $statement,
                'namespace' => $namespace,
                'file_location' => $fileLocation,
                'uses' => $uses
            ]);
            $namespace = $event->getArgument('namespace');
            $fileLocation = $event->getArgument('file_location');
            $uses = $event->getArgument('uses');
            $statement = $event->getStatement();
        }

        $code = $this->prettyPrinter->pStmt_Class($statement);
        $code =
            '<?php'.
            PHP_EOL.PHP_EOL.
            $namespace .
            PHP_EOL.
            $uses.
            PHP_EOL.
            $comments.
            PHP_EOL.
            $code.
            PHP_EOL;
        $name = $statement->name;

        if (!is_dir($fileLocation)) {
            // dir doesn't exist, make it
            mkdir($fileLocation, 0777, true);
        }

        file_put_contents(sprintf('%s/%s.php', $fileLocation, $name), $code);
    }

    protected function dispatch($eventName, array $arguments = [])
    {
        $event = new ClassyFileEvent(null, $arguments);
        $this->dispatcher->dispatch($eventName, $event);
        return $event;
    }
}
