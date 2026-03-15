<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

use SortedLinkedList\Exception\TypeMismatchException;

final class StringComparator implements ComparatorInterface
{
    public function compare(int|string $a, int|string $b): int
    {
        $this->validate($a);
        $this->validate($b);

        return strcmp($a, $b);
    }

    /**
     * @phpstan-assert string $value
     */
    public function validate(mixed $value): void
    {
        if (!is_string($value)) {
            throw TypeMismatchException::expectedString($value);
        }
    }
}
