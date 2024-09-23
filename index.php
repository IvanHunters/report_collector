<?php


use Lichi\Report\Extractor\ExcelExtractor;
use Lichi\Report\Reporter;
use Lichi\Report\test\TestValidator;

require "vendor/autoload.php";

$collector = (new ExcelExtractor)->extract("tmp/test.xlsx", [0,2]);
$count = $collector->count();
$header = $collector->header();
$data = $collector->get([]);

$validator = new TestValidator();

$collector->validate($validator);
if ($collector->hasErrors()) {
    $reporter = new Reporter($collector, false);
    $reporter->createReport('tmp/test_new.xlsx');
}