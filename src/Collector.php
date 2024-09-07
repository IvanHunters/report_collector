<?php

declare(strict_types=1);

namespace Lichi\Report;

class Collector
{
    private array $pipelines = [
        'data' => [],
        'errors' => [],
    ];
    public function add(Pipeline $pipeline): void
    {
        $this->pipelines['data'][] = $pipeline;
    }

    public function header(string $numerationColumnName = "", string $errorColumnName = ""): array
    {
        $reportHeader = [];
        if (!empty($numerationColumnName)) {
            $headers = [
                $numerationColumnName
            ];
        } else {
            $headers = [];
        }
        $pipelines = $this->pipelines['data'];
        foreach ($pipelines as $pipeline) {
            $dataHeader = $pipeline->getDataHeader();
            $headers = array_merge($headers, $dataHeader);
        }
        $headers = array_unique($headers);

        if (!empty($errorColumnName)) {
            $headers[] = $errorColumnName;
        }

        foreach ($headers as $header) {
            $reportHeader[$header] = 'string';
        }
        return $reportHeader;
    }

    public function pipelines(): array
    {
        return $this->pipelines['data'];
    }

    public function pipelineErrors(): array
    {
        return $this->pipelines['errors'];
    }

    public function validate(Validator $validator)
    {
        $pipelines = $this->pipelines['data'];
        foreach ($pipelines as $index => $pipeline) {
            $isValid = $validator->validate($pipeline);
            if (!$isValid) {
                $this->pipelines['errors'][$index] = $pipeline->getErrors();
            }
        }
    }

    public function count(): int
    {
        return count($this->pipelines['data']);
    }

    public function hasErrors(): bool
    {
        return !empty($this->pipelines['errors']);
    }

    public function get(array $arrayMap = []): array
    {
        $data = [];
        $pipelines = $this->pipelines['data'];
        foreach ($pipelines as $pipeline) {
            $items = $pipeline->getData();
            if (!empty($arrayMap)) {
                foreach ($items as $key => $item) {
                    if (isset($arrayMap[$key])) {
                        $newKey = $arrayMap[$key];
                        unset($items[$key]);
                        $items[$newKey] = $item;
                    } else {
                        unset($items[$key]);
                    }
                }
            }
            $data[] = $items;
        }
        return $data;
    }
}
