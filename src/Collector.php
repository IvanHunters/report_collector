<?php

declare(strict_types=1);

namespace Lichi\Report;

class Collector
{
    private array $data = [];
    public function add(PipelineData $pipelineData): void
    {
        $this->data[] = $pipelineData;
    }

    public function header(string $numerationColumnName, string $errorColumnName): array
    {
        $reportHeader = [];
        $headers = [
            $numerationColumnName
        ];
        foreach ($this->data as $datum) {
            $dataHeader = $datum->getDataHeader();
            $headers = array_merge($headers, $dataHeader);
        }
        $headers = array_unique($headers);
        $headers[] = $errorColumnName;

        foreach ($headers as $header) {
            $reportHeader[$header] = 'string';
        }
        return $reportHeader;
    }

    public function data(): array
    {
        return $this->data;
    }

}
