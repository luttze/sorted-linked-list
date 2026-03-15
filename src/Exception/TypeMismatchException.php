<?php

declare(strict_types=1);

namespace SortedLinkedList\Exception;

use InvalidArgumentException;

final class TypeMismatchException extends InvalidArgumentException
{
    public static function expectedInteger(mixed $value): self
    {
        return new self(sprintf(
            'Expected integer value, got %s.',
            get_debug_type($value),
        ));
    }

    public static function expectedString(mixed $value): self
    {
        return new self(sprintf(
            'Expected string value, got %s.',
            get_debug_type($value),
        ));
    }
}