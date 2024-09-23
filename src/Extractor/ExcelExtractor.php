<?php

declare(strict_types=1);

namespace Lichi\Report\Extractor;

use Lichi\Report\Collector;
use Lichi\Report\Pipeline;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExcelExtractor
{

    public static function extract(string $filename, array $ignoringRows = []): Collector
    {
        $headers = [];
        $hiddenData = [];
        $collector = new Collector();

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $filePath = $filename;
        $spreadsheet = $reader->load($filePath);

        $activeSheet = $spreadsheet->getActiveSheet();
        $preData = $activeSheet->toArray();
        $activeSheetName = $activeSheet->getTitle();
        $otherSheets = $spreadsheet->getAllSheets();
        foreach ($otherSheets as $otherSheet) {
            $sheetTitle = $otherSheet->getTitle();
            if ($sheetTitle !== $activeSheetName) {
                $hiddenData['sheets'][$sheetTitle] = $otherSheet;
            }
        }
        $clearedData = [];
        if (!empty($ignoringRows)) {
            foreach ($preData as $index => $data) {
                if (!in_array($index, $ignoringRows)) {
                    $clearedData[] = $data;
                } else {
                    $hiddenData['raws'][$index] = $data;
                }
            }
        } else {
            $clearedData = $preData;
        }
        $preHeaders = $clearedData[0];
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
        $collector->setHiddenData($hiddenData);
        return $collector;
    }
}
