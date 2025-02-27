<?php

declare(strict_types=1);

namespace Doctrine\Tests\PHPUnitCompatibility;

use PHPUnit\Framework\MockObject\MockBuilder;

use function method_exists;

trait MockBuilderCompatibilityTools
{
    /**
     * @param class-string<TMockedType> $className
     * @param list<string>              $onlyMethods
     *
     * @return MockBuilder<TMockedType>
     *
     * @template TMockedType of object
     */
    private function getMockBuilderWithOnlyMethods(string $className, array $onlyMethods): MockBuilder
    {
        $builder = $this->getMockBuilder($className);

        return method_exists($builder, 'onlyMethods')
            ? $builder->onlyMethods($onlyMethods) // PHPUnit 8+
            : $builder->setMethods($onlyMethods); // PHPUnit 7
    }
}
