<?php

namespace App\Imports;

use App\Models\Charity;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CharitiesImport implements ToCollection, WithStartRow, WithChunkReading, WithCustomCsvSettings
{

    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t",
            'input_encoding' => 'ISO-8859-1',
            'use_bom' => true,
            'line_ending' => PHP_EOL,
        ];
    }

    public function collection(Collection $rows)
    {
        echo '-- Importing charity ' . now() . PHP_EOL;   

        foreach ($rows as $row) {

            Charity::UpdateOrInsert([
                'registration_number' => $row[0],
            ], [
                Str::snake("Charity Name") => $row[1],
                Str::snake("Charity Status") => $row[2],
                Str::snake("Effective Date of Status") => $this->transformDate($row[3]),
                Str::snake("Sanction") => $row[4],
                Str::snake("Designation Code") => $row[5],
                Str::snake("Category Code") => $row[6],
                Str::snake("Address") => $row[7],
                Str::snake("City") => $row[8],
                Str::snake("Province") => $row[9],
                Str::snake("Country") => $row[10],
                Str::snake("Postal Code") => $row[11],
            ]);
        }
        
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        /* try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) { */
            return \Carbon\Carbon::createFromFormat($format, $value);
        // }
    }

    public function chunkSize(): int
    {
        return 1000;
    }


}
