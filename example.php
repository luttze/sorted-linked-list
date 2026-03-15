<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use SortedLinkedList\SortedLinkedList;

$list = SortedLinkedList::ofIntegers();
$list->add(5)->add(1)->add(3)->add(2);

echo implode(', ', $list->toArray()) . PHP_EOL;
echo 'First: ' . $list->first() . PHP_EOL;
echo 'Last: ' . $list->last() . PHP_EOL;
echo 'Count: ' . $list->count() . PHP_EOL;
echo 'Contains 3: ' . ($list->contains(3) ? 'yes' : 'no') . PHP_EOL;

$list->remove(3);
echo 'After remove(3): ' . implode(', ', $list->toArray()) . PHP_EOL;

$strings = SortedLinkedList::ofStrings();
$strings->add('banana')->add('apple')->add('cherry');
echo 'Strings: ' . implode(', ', $strings->toArray()) . PHP_EOL;
