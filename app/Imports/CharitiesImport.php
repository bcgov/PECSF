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

        $index = [];

        foreach ($rows as $row) {

            if(empty($row[4]))
            {
                continue;
            }

            try{
                Charity::UpdateOrInsert([
                    'registration_number' => $row[0],
                ], [
                    Str::snake("Charity Name") => $row[1],
                    Str::snake("Charity Status") =>  $row[2],
                    Str::snake("Type of qualified donee") =>  $row[3],
                    Str::snake("Effective Date of Status") => $this->transformDate($row[4]),
                    Str::snake("Sanction") => $row[5],
                    Str::snake("Designation Code") => $row[6],
                    Str::snake("Charity Type") => $row[7],
                    Str::snake("Category Code") => $row[8],
                    Str::snake("Address") => $row[9],
                    Str::snake("City") => $row[10],
                    Str::snake("Province") => $row[11],
                    Str::snake("Country") => $row[12],
                    Str::snake("Postal Code") => $row[13],
                ]);
            }
            catch(Exception $e){
                continue;
            }

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

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }


}
