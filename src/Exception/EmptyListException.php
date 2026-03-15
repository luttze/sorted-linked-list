<?php

declare(strict_types=1);

namespace SortedLinkedList\Exception;

use UnderflowException;

final class EmptyListException extends UnderflowException
{
    public static function noFirstElement(): self
    {
        return new self('Cannot get first element of an empty list.');
    }

    public static function noLastElement(): self
    {
        return new self('Cannot get last element of an empty list.');
    }
}
