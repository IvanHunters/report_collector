<?php

declare(strict_types=1);

namespace Lichi\Report;

use Lichi\Report\Render\ExcelRender;

class Reporter
{
    private Collector $collector;
    private bool $onlyErrors;

    public function __construct(Collector $collector, bool $onlyErrors = false)
    {
        $this->collector = $collector;
        $this->onlyErrors = $onlyErrors;
    }
    public function createReport(string $filename, string $numerationColumnName = "№", string $errorColumnName = "Errors"): void
    {
        $pipelines = $this->collector->pipelines();
        $headers = $this->collector->header($numerationColumnName, $errorColumnName);
        $mainHeaderStyles = ['font'=>'Times New Roman','font-size'=>12,'border'=>'left,right,top,bottom', 'halign'=> 'left', 'valign'=> 'center', 'border-style'=>'thin'];
        $bodyStyles = $mainHeaderStyles + ['height'=>17];
        $tagStyles = array('wrap_text'=>true, 'height'=>25, 'font'=>'Times New Roman', 'valign'=> 'center', 'font-style'=>'bold','font-size'=>12,'border'=>'none', 'halign'=> 'center');

        $rowDates = [];
        foreach ($pipelines as $index => $pipeline) {
            $data = $pipeline->getData();
            $error = $pipeline->getErrors();
            if (($this->onlyErrors && !empty($error)) || !($this->onlyErrors)) {
                foreach ($headers as $name => $header) {
                    if (trim($name) === $numerationColumnName) {
                        continue;
                    }
                    if (isset($data[$name]) && trim($name) !== $errorColumnName) {
                        $rowDates[$index][] = $data[$name];
                    } elseif ($name === $errorColumnName) {
                        $rowDates[$index][] = implode("    |    ", $error);
                    } else {
                        $rowDates[$index][] = '';
                    }
                }
            }
        }
        $widths = [];
        foreach ($headers as $headerName => $headerType) {
            if ($headerName === $numerationColumnName) {
                $widths[] = 5;
            } elseif($headerName === $errorColumnName) {
                $widths[] = 500;
            } else {
                $widths[] = 2.5 * mb_strlen($headerName);
            }
        }

        $headerStyles = $mainHeaderStyles + ['wrap_text'=>true, 'widths'=>$widths];

        $render = new ExcelRender($headers, $headerStyles, $numerationColumnName);
        $render->addBlock('Sheet1', null, $rowDates, $bodyStyles, $tagStyles);
        $render->save($filename);
    }
}
