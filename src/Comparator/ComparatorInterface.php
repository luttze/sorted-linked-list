<?php

declare(strict_types=1);

namespace SortedLinkedList\Comparator;

/**
 * Strategy interface for comparing and validating values.
 *
 * Separating comparison logic allows the list to remain open for extension
 * (e.g. case-insensitive strings, locale-aware sorting) without modification. (OCP)
 */
interface ComparatorInterface
{
    /**
     * Returns negative if $a < $b, zero if equal, positive if $a > $b.
     */
    public function compare(int|string $a, int|string $b): int;

    /**
     * Validates that the value is of the expected type.
     *
     * The parameter is intentionally typed as `mixed` so the comparator
     * can throw a domain-specific TypeMismatchException instead of PHP
     * throwing a native TypeError before the validation logic runs.
     *
     * @throws \SortedLinkedList\Exception\TypeMismatchException
     */
    public function validate(mixed $value): void;
}
