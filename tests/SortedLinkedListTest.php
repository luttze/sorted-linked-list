<?php

declare(strict_types=1);

namespace SortedLinkedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedLinkedList\Exception\EmptyListException;
use SortedLinkedList\Exception\TypeMismatchException;
use SortedLinkedList\Exception\ValueNotFoundException;
use SortedLinkedList\SortedLinkedList;

final class SortedLinkedListTest extends TestCase
{
    // ──────────────────────────────────────────────
    //  Integer list
    // ──────────────────────────────────────────────

    public function testIntegerListMaintainsSortOrder(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(5)->add(1)->add(3)->add(2)->add(4);

        self::assertSame([1, 2, 3, 4, 5], $list->toArray());
    }

    public function testIntegerListRejectStrings(): void
    {
        $list = SortedLinkedList::ofIntegers();

        $this->expectException(TypeMismatchException::class);
        $list->add('hello');
    }

    public function testIntegerListHandlesDuplicates(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(3)->add(1)->add(3)->add(1);

        self::assertSame([1, 1, 3, 3], $list->toArray());
    }

    public function testIntegerListHandlesNegativeNumbers(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(0)->add(-5)->add(10)->add(-3);

        self::assertSame([-5, -3, 0, 10], $list->toArray());
    }

    // ──────────────────────────────────────────────
    //  String list
    // ──────────────────────────────────────────────

    public function testStringListMaintainsSortOrder(): void
    {
        $list = SortedLinkedList::ofStrings();
        $list->add('banana')->add('apple')->add('cherry');

        self::assertSame(['apple', 'banana', 'cherry'], $list->toArray());
    }

    public function testStringListRejectsIntegers(): void
    {
        $list = SortedLinkedList::ofStrings();

        $this->expectException(TypeMismatchException::class);
        $list->add(42);
    }

    // ──────────────────────────────────────────────
    //  Remove
    // ──────────────────────────────────────────────

    public function testRemoveExistingValue(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3);
        $list->remove(2);

        self::assertSame([1, 3], $list->toArray());
    }

    public function testRemoveHead(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3);
        $list->remove(1);

        self::assertSame([2, 3], $list->toArray());
    }

    public function testRemoveTail(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3);
        $list->remove(3);

        self::assertSame([1, 2], $list->toArray());
    }

    public function testRemoveOnlyFirstOccurrence(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(1)->add(2);
        $list->remove(1);

        self::assertSame([1, 2], $list->toArray());
    }

    public function testRemoveNonExistentValueThrows(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(3);

        $this->expectException(ValueNotFoundException::class);
        $list->remove(2);
    }

    public function testRemoveFromEmptyListThrows(): void
    {
        $list = SortedLinkedList::ofIntegers();

        $this->expectException(ValueNotFoundException::class);
        $list->remove(1);
    }

    public function testRemoveAllOccurrences(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(2)->add(2)->add(3);
        $list->removeAll(2);

        self::assertSame([1, 3], $list->toArray());
    }

    // ──────────────────────────────────────────────
    //  Contains
    // ──────────────────────────────────────────────

    public function testContainsExistingValue(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3);

        self::assertTrue($list->contains(2));
    }

    public function testDoesNotContainMissingValue(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(3);

        self::assertFalse($list->contains(2));
    }

    public function testContainsValidatesType(): void
    {
        $list = SortedLinkedList::ofIntegers();

        $this->expectException(TypeMismatchException::class);
        $list->contains('nope');
    }

    // ──────────────────────────────────────────────
    //  First / Last / Empty
    // ──────────────────────────────────────────────

    public function testFirstAndLast(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(5)->add(1)->add(9);

        self::assertSame(1, $list->first());
        self::assertSame(9, $list->last());
    }

    public function testFirstOnEmptyListThrows(): void
    {
        $this->expectException(EmptyListException::class);
        SortedLinkedList::ofIntegers()->first();
    }

    public function testLastOnEmptyListThrows(): void
    {
        $this->expectException(EmptyListException::class);
        SortedLinkedList::ofIntegers()->last();
    }

    public function testIsEmpty(): void
    {
        $list = SortedLinkedList::ofIntegers();
        self::assertTrue($list->isEmpty());

        $list->add(1);
        self::assertFalse($list->isEmpty());
    }

    // ──────────────────────────────────────────────
    //  Count / Iterable
    // ──────────────────────────────────────────────

    public function testCount(): void
    {
        $list = SortedLinkedList::ofIntegers();
        self::assertCount(0, $list);

        $list->add(1)->add(2)->add(3);
        self::assertCount(3, $list);
    }

    public function testIsIterable(): void
    {
        $list = SortedLinkedList::ofStrings();
        $list->add('c')->add('a')->add('b');

        $collected = [];
        foreach ($list as $value) {
            $collected[] = $value;
        }

        self::assertSame(['a', 'b', 'c'], $collected);
    }

    // ──────────────────────────────────────────────
    //  Clear
    // ──────────────────────────────────────────────

    public function testClear(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3);
        $list->clear();

        self::assertTrue($list->isEmpty());
        self::assertCount(0, $list);
        self::assertSame([], $list->toArray());
    }

    // ──────────────────────────────────────────────
    //  Merge
    // ──────────────────────────────────────────────

    public function testMergeTwoLists(): void
    {
        $a = SortedLinkedList::ofIntegers();
        $a->add(1)->add(4)->add(7);

        $b = SortedLinkedList::ofIntegers();
        $b->add(2)->add(5)->add(8);

        $a->merge($b);

        self::assertSame([1, 2, 4, 5, 7, 8], $a->toArray());
    }

    // ──────────────────────────────────────────────
    //  Filter
    // ──────────────────────────────────────────────

    public function testFilterReturnsNewFilteredList(): void
    {
        $list = SortedLinkedList::ofIntegers();
        $list->add(1)->add(2)->add(3)->add(4)->add(5);

        $evens = $list->filter(fn(int $v): bool => $v % 2 === 0);

        self::assertSame([2, 4], $evens->toArray());
        self::assertCount(5, $list); // original unchanged
    }

    // ──────────────────────────────────────────────
    //  Fluent API
    // ──────────────────────────────────────────────

    public function testFluentChaining(): void
    {
        $result = SortedLinkedList::ofIntegers()
            ->add(3)
            ->add(1)
            ->add(2)
            ->remove(1)
            ->toArray();

        self::assertSame([2, 3], $result);
    }
}
