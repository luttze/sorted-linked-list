<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use SortedLinkedList\Exception\TypeMismatchException;

final class IntegerComparator implements ComparatorInterface
{
    public function compare(int|string $a, int|string $b): int
    {
        /** @var int $a */
        /** @var int $b */
        return $a <=> $b;
    }

    public function validate(mixed $value): void
    {
        if (!is_int($value)) {
            throw TypeMismatchException::expectedInteger($value);
        }
    }
}
