<?php

declare(strict_types=1);

namespace Doctrine\ORM\Query\Expr;

use InvalidArgumentException;
use Stringable;

use function count;
use function get_class;
use function get_debug_type;
use function implode;
use function in_array;
use function is_string;
use function sprintf;

/**
 * Abstract base Expr class for building DQL parts.
 *
 * @link    www.doctrine-project.org
 */
abstract class Base
{
    /** @var string */
    protected $preSeparator = '(';

    /** @var string */
    protected $separator = ', ';

    /** @var string */
    protected $postSeparator = ')';

    /** @var list<class-string> */
    protected $allowedClasses = [];

    /** @var list<string|Stringable> */
    protected $parts = [];

    /** @param mixed $args */
    public function __construct($args = [])
    {
        $this->addMultiple($args);
    }

    /**
     * @param string[]|object[]|string|object $args
     * @phpstan-param list<string|object>|string|object $args
     *
     * @return $this
     */
    public function addMultiple($args = [])
    {
        foreach ((array) $args as $arg) {
            $this->add($arg);
        }

        return $this;
    }

    /**
     * @param mixed $arg
     *
     * @return $this
     *
     * @throws InvalidArgumentException
     */
    public function add($arg)
    {
        if ($arg !== null && (! $arg instanceof self || $arg->count() > 0)) {
            // If we decide to keep Expr\Base instances, we can use this check
            if (! is_string($arg) && ! in_array(get_class($arg), $this->allowedClasses, true)) {
                throw new InvalidArgumentException(sprintf(
                    "Expression of type '%s' not allowed in this context.",
                    get_debug_type($arg)
                ));
            }

            $this->parts[] = $arg;
        }

        return $this;
    }

    /**
     * @return int
     * @phpstan-return 0|positive-int
     */
    public function count()
    {
        return count($this->parts);
    }

    /** @return string */
    public function __toString()
    {
        if ($this->count() === 1) {
            return (string) $this->parts[0];
        }

        return $this->preSeparator . implode($this->separator, $this->parts) . $this->postSeparator;
    }
}
