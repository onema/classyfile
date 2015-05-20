<?php
/*
 * This file is part of the Onema {test} Package. 
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */
namespace Onema\ClassyFile\Plugin;

use Onema\ClassyFile\ClassyFileEvent;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ConstantNamesToUpper - Traverses the statements of a class and
 * converts all constant names to uppercase words separated by underscores.
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, onema.io
 */
class ConstantNamesToUpper implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [ClassyFileEvent::SET_CLASS => ['onSetClassUpdateConstants', 10]];
    }

    /**
     * Iterate over class statements looking for class constants. It then makes the constant name uppercase
     * separating each word by underscores.
     *
     * @param ClassyFileEvent $event
     */
    public function onSetClassUpdateConstants(ClassyFileEvent $event)
    {
        $statements = $event->getStatement();
        if ($statements instanceof Class_) {
            $count = count($statements->stmts);
            for ($i = 0; $i < $count; $i++) {

                if (is_array($statements->stmts)) {
                    $statement = $statements->stmts[$i];
                    $this->toUpper($statement);
                    $statements->stmts[$i] = $statement;
                } else {
                    $statement = $statements->stmts;
                    $this->toUpper($statement);
                    $statements->stmts = $statement;
                }
            }
        }

        $event->setStatement($statements);
    }

    private function toUpper($statement)
    {
        if ($statement instanceof ClassConst) {
            $countConst = count($statement->consts);
            for ($j = 0; $j < $countConst; $j++) {
                $constant = $statement->consts[$j];
                $name = preg_replace('/(?<=\\w)(?=[A-Z])/',"_$1", $constant->name);
                $constant->name = strtoupper($name);
                $statement->consts[$j] = $constant;
            }
        }
        return $statement;
    }
}
