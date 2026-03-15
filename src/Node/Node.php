<?php

declare(strict_types=1);

namespace SortedLinkedList\Node;

/**
 * Internal node of the linked list. Not part of the public API.
 *
 * @internal
 */
final class Node
{
    public ?self $next = null;

    public function __construct(
        public readonly int|string $value,
    ) {
    }
}
