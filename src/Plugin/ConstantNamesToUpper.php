<?php
/*
 * This file is part of the Onema ClassyFile Package.
 * For the full copyright and license information,
 * please view the LICENSE file that was distributed
 * with this source code.
 */

namespace Onema\ClassyFile\Plugin;

use Onema\ClassyFile\Event\ClassyFileEvent;
use Onema\ClassyFile\Event\GetClassEvent;
use PhpParser\Node\Const_;
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
        return [GetClassEvent::BEFORE => ['onSetClassUpdateConstants', 10]];
    }

    /**
     * Iterate over class statements looking for class constants. It then makes the constant name uppercase
     * separating each word by underscores.
     *
     * @param ClassyFileEvent $event
     */
    public function onSetClassUpdateConstants(ClassyFileEvent $event)
    {
        $statements = $event->getStatements();
        if ($statements instanceof Class_) {
            $count = count($statements->stmts);
            for ($i = 0; $i < $count; $i++) {
                if (is_array($statements->stmts)) {
                    $statement = $statements->stmts[$i];
                    $this->checkClassConstant($statement);
                    $statements->stmts[$i] = $statement;
                }
            }
        }

        $event->setStatements($statements);
    }

    /**
     * Converts class constant names to uppercase separated by underscores. Ignores all uppercase constants.
     *
     * @param $statement
     * @return mixed
     */
    private function checkClassConstant($statement)
    {
        if ($statement instanceof ClassConst) {
            $countConst = count($statement->consts);
            for ($j = 0; $j < $countConst; $j++) {
                $this->toUpper($statement->consts[$j]);
            }
        }

        return $statement;
    }

    private function toUpper(Const_ $constant)
    {
        if (strtoupper($constant->name) !== $constant->name) {
            preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $constant->name, $matches);
            $ret = $matches[0];
            foreach ($ret as &$match) {
                $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
            }
            $constant->name = strtoupper(implode('_', $ret));
        }

        return $constant;
    }
}
