<?php

declare(strict_types=1);

namespace Doctrine\ORM\Mapping;

use Attribute;
use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;
use Doctrine\ORM\EntityRepository;

/**
 * @Annotation
 * @NamedArgumentConstructor()
 * @Target("CLASS")
 * @template T of object
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Entity implements MappingAttribute
{
    /**
     * @var class-string<EntityRepository<T>>|null
     * @readonly
     */
    public $repositoryClass;

    /**
     * @var bool
     * @readonly
     */
    public $readOnly = false;

    /** @phpstan-param class-string<EntityRepository<T>>|null $repositoryClass */
    public function __construct(?string $repositoryClass = null, bool $readOnly = false)
    {
        $this->repositoryClass = $repositoryClass;
        $this->readOnly        = $readOnly;
    }
}
