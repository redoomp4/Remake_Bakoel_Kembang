<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ArrayExport implements FromArray, WithHeadings
{
    protected array $data;

    protected ?array $headings;

    public function __construct(array $data, ?array $headings = null)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function array(): array
    {
        return $this->data;
    }

    public function headings(): array
    {
        if ($this->headings !== null) {
            return $this->headings;
        }

        if (! empty($this->data)) {
            $first = reset($this->data);
            if (is_array($first)) {
                return array_keys($first);
            }
        }

        return [];
    }
}
