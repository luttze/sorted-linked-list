<?php

declare(strict_types=1);

namespace SortedLinkedList\Exception;

use RuntimeException;

final class ValueNotFoundException extends RuntimeException
{
    public static function forValue(int|string $value): self
    {
        return new self(sprintf(
            'Value "%s" was not found in the list.',
            $value,
        ));
    }
}
