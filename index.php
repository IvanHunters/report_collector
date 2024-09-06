<?php


use Lichi\Report\Collector;
use Lichi\Report\PipelineData;
use Lichi\Report\Reporter;

require "vendor/autoload.php";

$collector = new Collector();
$items = [
    ['name' => 'test'],
    ['name' => 'test2'],
    ['name' => 'test3', 'pos' => '123'],
    ['name' => 'test4', 'tos' => 'asd'],
];

foreach ($items as $index => $item) {
    $pipeline = new PipelineData($item);
    if ($index % 2 === 0) {
        $pipeline->addError('Bad roll');
        $pipeline->addError('Bad error');
    } else {
        $pipeline->addError('Bad lose');
    }
    $collector->add($pipeline);
}

$reporter = new Reporter($collector);

$reporter->createReport('test.xlsx');