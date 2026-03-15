<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use SortedLinkedList\Exception\TypeMismatchException;

final class StringComparator implements ComparatorInterface
{
    public function compare(int|string $a, int|string $b): int
    {
        /** @var string $a */
        /** @var string $b */
        return strcmp($a, $b);
    }

    public function validate(mixed $value): void
    {
        if (!is_string($value)) {
            throw TypeMismatchException::expectedString($value);
        }
    }
}
