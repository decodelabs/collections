<?php

/**
 * @package PHPStanDecodeLabs
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\PHPStan\Collections;

use DecodeLabs\Collections\Tree;
use DecodeLabs\PHPStan\PropertyReflection;

use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection as PropertyReflectionInterface;
use PHPStan\Type\ObjectType;

class TreeReflectionExtension implements PropertiesClassReflectionExtension
{
    public function hasProperty(
        ClassReflection $classReflection,
        string $propertyName
    ): bool {
        return
            $classReflection->is(Tree::class) ||
            $classReflection->isSubclassOf(Tree::class);
    }

    public function getProperty(
        ClassReflection $classReflection,
        string $propertyName
    ): PropertyReflectionInterface {
        return new PropertyReflection(
            $classReflection,
            new ObjectType($classReflection->getName()),
            $classReflection->getMethod('__set', new OutOfClassScope())->getVariants()[0]->getParameters()[1]->getType()
        );
    }
}
