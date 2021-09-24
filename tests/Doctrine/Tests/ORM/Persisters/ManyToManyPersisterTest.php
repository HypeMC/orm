<?php

declare(strict_types=1);

namespace Doctrine\Tests\ORM\Persisters;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Persisters\Collection\ManyToManyPersister;
use Doctrine\Tests\Mocks\ConnectionMock;
use Doctrine\Tests\Models\ManyToManyPersister\ChildClass;
use Doctrine\Tests\Models\ManyToManyPersister\OtherParentClass;
use Doctrine\Tests\Models\ManyToManyPersister\ParentClass;
use Doctrine\Tests\Models\ManyToManyPersister\Post;
use Doctrine\Tests\Models\ManyToManyPersister\Tag;
use Doctrine\Tests\OrmTestCase;

use function array_pop;
use function assert;

/**
 * @covers \Doctrine\ORM\Persisters\Collection\ManyToManyPersister
 */
final class ManyToManyPersisterTest extends OrmTestCase
{
    /**
     * @group GH-6991
     * @group ManyToManyPersister
     */
    public function testDeleteManyToManyCollection(): void
    {
        $parent      = new ParentClass(1);
        $otherParent = new OtherParentClass(42);
        $child       = new ChildClass(1, $otherParent);

        $parent->children->add($child);
        $child->parents->add($parent);

        $em = $this->getTestEntityManager();
        $em->persist($parent);
        $em->flush();

        $childReloaded = $em->find(ChildClass::class, ['id1' => 1, 'otherParent' => $otherParent]);
        assert($childReloaded instanceof ChildClass || $childReloaded === null);

        self::assertNotNull($childReloaded);

        $persister = new ManyToManyPersister($em);
        $persister->delete($childReloaded->parents);

        $conn = $em->getConnection();
        assert($conn instanceof ConnectionMock);

        $updates    = $conn->getExecuteUpdates();
        $lastUpdate = array_pop($updates);

        self::assertEquals('DELETE FROM parent_child WHERE child_id1 = ? AND child_id2 = ?', $lastUpdate['query']);
        self::assertEquals([1, 42], $lastUpdate['params']);
    }

    /**
     * @group ManyToManyPersister
     */
    public function testLoadCriteria(): void
    {
        $post = new Post(1, [
            new Tag(1, 'TagText1'),
            new Tag(2, 'TagText2'),
            new Tag(3, 'TagColor1'),
            new Tag(4, 'TagColor2'),
            new Tag(5, 'TagSomethingElse'),
        ]);

        $em = $this->getTestEntityManager();
        $em->persist($post);
        $em->flush();

        $postReloaded = $em->find(Post::class, 1);
        assert($postReloaded instanceof Post || $postReloaded === null);

        self::assertNotNull($postReloaded);

        $criteria = Criteria::create()->where(Criteria::expr()->startsWith('name', 'TagText'));

        $persister = new ManyToManyPersister($em);
        $persister->loadCriteria($postReloaded->children, $criteria);

        $conn = $em->getConnection();
        assert($conn instanceof ConnectionMock);

        $queries   = $conn->getExecuteQueries();
        $lastQuery = array_pop($queries);

        self::assertEquals('SELECT te.id AS id, te.name AS name FROM manytomanypersister_tag te JOIN post_tag t ON t.tag_id = te.id WHERE t.post_id = ? AND te.name LIKE ?', $lastQuery['query']);
        self::assertEquals([1, 'TagText%'], $lastQuery['params']);
    }
}
