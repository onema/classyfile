<?php
/*
 * This file is part of the Onema {classyfile} Package.
 * For the full copyright and license information, 
 * please view the LICENSE file that was distributed 
 * with this source code.
 */

namespace Onema\Test;
use Onema\ClassyFile\Template\BasicClassTemplate;

/**
 * BasicClassTemplateTest - Description. 
 *
 * @author Juan Manuel Torres <kinojman@gmail.com>
 * @copyright (c) 2015, Onema
 * @group template
 */
class BasicClassTemplateTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaultTest ()
    {
        $date = new \DateTime();
        $templateClass = new BasicClassTemplate();
        $template = $templateClass->getTemplate('foo', 'bar', 'blah', 'lala');
        $this->assertRegExp(sprintf('/%s/', $date->format('Y')), $template);
        $this->assertRegExp(sprintf('/%s/', 'Juan Manuel Torres'), $template);
    }

    public function testCustomTest ()
    {
        $expectedTemplate = sprintf('<?php%s%s%s%s%s%s%s%s%s%s%s', PHP_EOL, '', PHP_EOL, 'foo', PHP_EOL, 'bar', PHP_EOL, 'blah', PHP_EOL, 'lala', PHP_EOL);
        $templateClass = new BasicClassTemplate('');
        $template = $templateClass->getTemplate('foo', 'bar', 'blah', 'lala');

        $this->assertEquals($expectedTemplate, $template);
    }
}
