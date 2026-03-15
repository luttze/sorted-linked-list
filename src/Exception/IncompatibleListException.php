<?php

declare(strict_types=1);

namespace SortedLinkedList\Exception;

use LogicException;

final class IncompatibleListException extends LogicException
{
    public static function differentComparators(string $expected, string $actual): self
    {
        return new self(sprintf(
            'Cannot merge lists with different comparators: expected %s, got %s.',
            $expected,
            $actual,
        ));
    }
}
