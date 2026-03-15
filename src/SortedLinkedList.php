<?php

declare(strict_types=1);

namespace SortedLinkedList;

use Countable;
use IteratorAggregate;
use SortedLinkedList\Comparator\ComparatorInterface;
use SortedLinkedList\Comparator\IntegerComparator;
use SortedLinkedList\Comparator\StringComparator;
use SortedLinkedList\Exception\EmptyListException;
use SortedLinkedList\Exception\IncompatibleListException;
use SortedLinkedList\Node\Node;
use Traversable;

/**
 * A linked list that maintains its elements in sorted order.
 *
 * Type-safe: holds either integers or strings, never both.
 * Use the named constructors to create an instance.
 *
 * @implements IteratorAggregate<int, int|string>
 */
final class SortedLinkedList implements IteratorAggregate, Countable
{
    private ?Node $head = null;
    private int $size = 0;

    /**
     * Use named constructors instead.
     */
    private function __construct(
        private readonly ComparatorInterface $comparator,
    ) {
    }

    // ──────────────────────────────────────────────
    //  Named constructors
    // ──────────────────────────────────────────────

    public static function ofIntegers(): self
    {
        return new self(new IntegerComparator());
    }

    public static function ofStrings(): self
    {
        return new self(new StringComparator());
    }

    /**
     * Create a list with a custom comparator (e.g. case-insensitive strings, reverse order).
     *
     * The comparator is still bound to int|string by the interface contract.
     */
    public static function withComparator(ComparatorInterface $comparator): self
    {
        return new self($comparator);
    }

    // ──────────────────────────────────────────────
    //  Commands
    // ──────────────────────────────────────────────

    public function add(int|string $value): self
    {
        $this->comparator->validate($value);

        $newNode = new Node($value);

        // Insert at head if list is empty or value goes before current head.
        if ($this->head === null || $this->comparator->compare($value, $this->head->value) <= 0) {
            $newNode->next = $this->head;
            $this->head = $newNode;
            $this->size++;

            return $this;
        }

        // Walk to the insertion point.
        $current = $this->head;
        while ($current->next !== null && $this->comparator->compare($current->next->value, $value) < 0) {
            $current = $current->next;
        }

        $newNode->next = $current->next;
        $current->next = $newNode;
        $this->size++;

        return $this;
    }

    /**
     * Remove the first occurrence of the given value.
     *
     * @return bool True if the value was found and removed, false otherwise.
     */
    public function remove(int|string $value): bool
    {
        $this->comparator->validate($value);

        if ($this->head === null) {
            return false;
        }

        if ($this->comparator->compare($this->head->value, $value) === 0) {
            $this->head = $this->head->next;
            $this->size--;

            return true;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($this->comparator->compare($current->next->value, $value) === 0) {
                $current->next = $current->next->next;
                $this->size--;

                return true;
            }

            if ($this->comparator->compare($current->next->value, $value) > 0) {
                break;
            }

            $current = $current->next;
        }

        return false;
    }

    /**
     * Remove all occurrences of the given value.
     */
    public function removeAll(int|string $value): self
    {
        $this->comparator->validate($value);

        while ($this->head !== null && $this->comparator->compare($this->head->value, $value) === 0) {
            $this->head = $this->head->next;
            $this->size--;
        }

        $current = $this->head;

        while ($current !== null && $current->next !== null) {
            $comparison = $this->comparator->compare($current->next->value, $value);

            if ($comparison === 0) {
                $current->next = $current->next->next;
                $this->size--;
                continue;
            }

            if ($comparison > 0) {
                break;
            }

            $current = $current->next;
        }

        return $this;
    }

    public function clear(): self
    {
        $this->head = null;
        $this->size = 0;

        return $this;
    }

    // ──────────────────────────────────────────────
    //  Queries
    // ──────────────────────────────────────────────

    public function contains(int|string $value): bool
    {
        $this->comparator->validate($value);

        $current = $this->head;
        while ($current !== null) {
            $cmp = $this->comparator->compare($current->value, $value);

            if ($cmp === 0) {
                return true;
            }

            // Sorted — no need to keep looking.
            if ($cmp > 0) {
                return false;
            }

            $current = $current->next;
        }

        return false;
    }

    /**
     * @throws EmptyListException
     */
    public function first(): int|string
    {
        if ($this->head === null) {
            throw EmptyListException::noFirstElement();
        }

        return $this->head->value;
    }

    /**
     * @throws EmptyListException
     */
    public function last(): int|string
    {
        if ($this->head === null) {
            throw EmptyListException::noLastElement();
        }

        $current = $this->head;
        while ($current->next !== null) {
            $current = $current->next;
        }

        return $current->value;
    }

    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    /** @return int<0, max> */
    public function count(): int
    {
        /** @var int<0, max> */
        return $this->size;
    }

    /**
     * @return array<int, int|string>
     */
    public function toArray(): array
    {
        $result = [];
        $current = $this->head;

        while ($current !== null) {
            $result[] = $current->value;
            $current = $current->next;
        }

        return $result;
    }

    /**
     * @return Traversable<int, int|string>
     */
    public function getIterator(): Traversable
    {
        $current = $this->head;

        while ($current !== null) {
            yield $current->value;
            $current = $current->next;
        }
    }

    // ──────────────────────────────────────────────
    //  Derived operations
    // ──────────────────────────────────────────────

    /**
     * Merge another sorted list into this one (mutates $this, leaves $other unchanged).
     *
     * Both lists must use the same comparator type.
     * Complexity: O(n + m).
     *
     * @throws IncompatibleListException
     */
    public function merge(self $other): self
    {
        if ($other->head === null) {
            return $this;
        }

        $this->assertCompatible($other);

        $sentinel = new Node(0);
        $tail = $sentinel;

        $a = $this->head;
        $b = $other->head;

        while ($a !== null && $b !== null) {
            if ($this->comparator->compare($a->value, $b->value) <= 0) {
                $tail->next = $a;
                $a = $a->next;
            } else {
                $tail->next = new Node($b->value);
                $b = $b->next;
            }

            $tail = $tail->next;
        }

        $tail->next = $a;

        while ($tail->next !== null) {
            $tail = $tail->next;
        }

        while ($b !== null) {
            $tail->next = new Node($b->value);
            $tail = $tail->next;
            $b = $b->next;
        }

        $this->head = $sentinel->next;
        $this->size += $other->size;

        return $this;
    }

    private function assertCompatible(self $other): void
    {
        if ($this->comparator::class !== $other->comparator::class) {
            throw IncompatibleListException::differentComparators(
                $this->comparator::class,
                $other->comparator::class,
            );
        }
    }

    /**
     * Return a new list containing only values that satisfy the predicate.
     *
     * @param callable(int|string): bool $predicate
     */
    public function filter(callable $predicate): self
    {
        $filtered = new self($this->comparator);

        foreach ($this as $value) {
            if ($predicate($value)) {
                $filtered->add($value);
            }
        }

        return $filtered;
    }
}
