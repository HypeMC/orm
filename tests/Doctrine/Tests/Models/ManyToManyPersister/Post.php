<?php

declare(strict_types=1);

namespace Doctrine\Tests\Models\ManyToManyPersister;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="manytomanypersister_post")
 */
class Post
{
    /**
     * @var int
     * @Id
     * @Column(name="id", type="integer")
     */
    public $id;

    /**
     * @var Collection<Tag>
     * @ManyToMany(targetEntity=Tag::class, cascade={"persist"})
     */
    public $children;

    public function __construct(int $id, array $children)
    {
        $this->id       = $id;
        $this->children = new ArrayCollection($children);
    }
}
