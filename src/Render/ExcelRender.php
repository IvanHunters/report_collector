<?php

declare(strict_types=1);

namespace Lichi\Report\Render;

use XLSXWriter;

class ExcelRender
{
    private array $header;
    private array $headerStyles;
    private array $sheetData = [];

    public function __construct(array $header, array $headerStyles, string $numerationColumnName)
    {
        $this->header = $header;
        $this->headerStyles = $headerStyles;
    }

    public function addBlock(string $sheet, ?string $blockName, array $data, array $blockStyle, array $tagStyle)
    {
        $this->sheetData[$sheet][$blockName] = ['data' => $data, 'block_style' => $blockStyle, 'tag_style' => $tagStyle];
    }



    public function save(string $filename, array $hiddenRows = [], array $hiddenSheets = []): void
    {
        $writer = new XLSXWriter();
        $this->render($writer, $hiddenRows, $hiddenSheets);
        $writer->writeToFile($filename);
    }

    private function render(XLSXWriter $writer, array $hiddenRows = [], array $hiddenSheets = []){
        $sheetData = $this->sheetData;
        foreach ($sheetData as $sheetName => $blocks)
        {
            $rows = $blockRows = $realRows = 1;
            if (isset($hiddenRows[0])) {
                $hiddenRow = $hiddenRows[0];
                $realRows = 2;
                $writer->writeSheetRow($sheetName, $hiddenRow, ['wrap_text'=>true, 'height'=>25, 'font'=>'Times New Roman', 'valign'=> 'center', 'font-style'=>'bold','font-size'=>12,'border'=>'none', 'halign'=> 'center']);
            }
            $writer->writeSheetRow($sheetName, array_keys($this->header), $this->headerStyles);
            foreach ($blocks as $blockName => $blockItems)
            {
                $blockValues = $blockItems['data'];
                $blockStyle = $blockItems['block_style'];
                $tagStyle = $blockItems['tag_style'];


                if(!empty($blockName)) {
                    $writer->writeSheetRow($sheetName, [$blockName], $tagStyle);
                    $writer->markMergedCell($sheetName, $rows, $start_col = 0, $rows, $end_col = count($this->header) - 1);
                    $rows +=1;
                    $realRows+=1;
                }
                if (isset($hiddenRows[$realRows])) {
                    $hiddenRow = array_merge([null], $hiddenRows[$realRows]);
                    $writer->writeSheetRow($sheetName, $hiddenRow, $tagStyle);
                    $rows +=1;
                    $realRows+=1;
                }
                foreach ($blockValues as $blockValue)
                {
                    array_unshift($blockValue, (string) $blockRows);
                    $writer->writeSheetRow($sheetName, $blockValue, $blockStyle);
                    $rows +=1;
                    $blockRows +=1;
                    $realRows+=1;
                }
            }
        }
        foreach ($hiddenSheets as $sheetName => $sheet) {
            $writer->writeSheet($sheet, $sheetName);
        }


    }
}
