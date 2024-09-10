<?php

declare(strict_types=1);

namespace Lichi\Report\Extractor;

use Lichi\Report\Collector;
use Lichi\Report\Pipeline;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelExtractor
{

    public static function extract(string $filename): Collector
    {
        $headers = [];;
        $collector = new Collector();

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $filePath = $filename;
        $spreadsheet = $reader->load($filePath);

        $preData = $spreadsheet->getActiveSheet()->toArray();
        $preHeaders = $preData[0];
        foreach ($preHeaders as $preHeader) {
            if (!is_null($preHeader)) {
                $headers[] = $preHeader;
            }
        }
        unset($preData[0]);
        foreach ($preData as $items) {
            $item = [];
            if (empty(array_sum($items))) {
                continue;
            }
            foreach ($headers as $index => $header) {
                $item[$header] = $items[$index];
            }
            $pipeline = new Pipeline($item);
            $collector->add($pipeline);
        }
        return $collector;
    }

}
