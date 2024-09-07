<?php


use Lichi\Report\Collector;
use Lichi\Report\Pipeline;
use Lichi\Report\Reporter;
use Lichi\Report\test\TestValidator;

require "vendor/autoload.php";

$collector = new Collector();
$items = [
    ['name' => 'test'],
    ['name' => 'test2'],
    ['name' => 'test3', 'test' => '123'],
    ['name' => 'test4', 'tos' => 'asd'],
];

$validator = new TestValidator();
foreach ($items as $index => $item) {
    $pipeline = new Pipeline($item);
    $collector->add($pipeline);
}

$collector->validate($validator);
if ($collector->hasErrors()) {
    $reporter = new Reporter($collector, true);
    $reporter->createReport('test.xlsx');
}