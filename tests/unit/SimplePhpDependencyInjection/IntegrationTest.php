<?php
/**
 * This file is part of php-simple-di.
 *
 * php-simple-di is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * php-simple-di is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with php-simple-di.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Tests\SimplePhpDependencyInjection;

use Mcustiel\PhpSimpleDependencyInjection\DependencyContainer;
use Fixtures\FakeDependency;
use Fixtures\AnnotatedDependency;
use Fixtures\AnotherDependency;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The unit under test.
     *
     * @var \Mcustiel\PhpSimpleDependencyInjection\DependencyContainer
     */
    private $dependencyContainer;

    public function setUp()
    {
        $this->dependencyContainer = DependencyContainer::getInstance();
    }

    public function testDependencyContainerWhenDependencyExists()
    {
        $this->dependencyContainer->add(
            'fakeDependency',
            function ()
            {
                return new FakeDependency('someValue');
            }
        );

        $this->assertInstanceOf('\Fixtures\FakeDependency',
            $this->dependencyContainer->get('fakeDependency'));
        $this->assertEquals('someValue', $this->dependencyContainer->get('fakeDependency')
            ->getAValue());
    }

    public function testDependencyContainerWhitValuesFromOutsideTheClosure()
    {
        $theValue = 'outsideValue';
        $this->dependencyContainer->add(
            'fakeDependency',
            function () use($theValue)
            {
                return new FakeDependency($theValue);
            }
        );

        $this->assertEquals('outsideValue', $this->dependencyContainer->get('fakeDependency')
            ->getAValue());
    }

    public function testDependencyContainerSingleton()
    {
        $this->dependencyContainer->add(
            'fakeDependency',
            function ()
            {
                return new FakeDependency('someValue');
            }
        );

        $instance1 = $this->dependencyContainer->get('fakeDependency');
        $instance2 = $this->dependencyContainer->get('fakeDependency');
        $this->assertInstanceOf('\Fixtures\FakeDependency', $instance1);
        $this->assertInstanceOf('\Fixtures\FakeDependency', $instance2);

        $this->assertSame($instance1, $instance2);
    }

    public function testDependencyContainerNoSingleton()
    {
        $this->dependencyContainer->add(
            'fakeDependency',
            function ()
            {
                return new FakeDependency('someValue');
            },
            false
        );

        $instance1 = $this->dependencyContainer->get('fakeDependency');
        $instance2 = $this->dependencyContainer->get('fakeDependency');
        $this->assertInstanceOf('\Fixtures\FakeDependency', $instance1);
        $this->assertInstanceOf('\Fixtures\FakeDependency', $instance2);

        $this->assertFalse($this->areTheSame($instance1, $instance2));
    }

    /**
     * @expectedException \Mcustiel\PhpSimpleDependencyInjection\Exception\DependencyDoesNotExistException
     * @expectedExceptionMessage Dependency identified by 'doesNotExists' does not exist
     */
    public function testDependencyContainerWhenDependencyDoesNotExist()
    {
        $this->dependencyContainer->get('doesNotExists');
    }

    private function areTheSame(&$object1, &$object2)
    {
        return $object1 === $object2;
    }
}
