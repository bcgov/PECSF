<?php

namespace App\Imports;

use App\Models\Charity;

use Illuminate\Support\Collection;
//use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CharityURLsImport implements ToCollection, WithStartRow, WithCustomCsvSettings
{

    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            //'enclosure'  => '"',
            'input_encoding' => 'ISO-8859-1',
            'use_bom' => true,
            'line_ending' => PHP_EOL,
        ];
    }

    public function collection(Collection $rows)
    {

        echo '-- Update Website ' . now() . PHP_EOL;   

        foreach ($rows as $row) {

            Charity::where('registration_number', $row[0])
                     ->update(['url' => $row[2]]);

        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

}
