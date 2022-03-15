<?php

namespace App\Imports;

use App\Models\Charity;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CharityOngoingProgramsImport implements ToCollection, WithCustomCsvSettings, WithHeadingRow
{

    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            //'enclosure'  => '"',
            'input_encoding' => 'ISO-8859-1',
            'use_bom' => true,
            //'line_ending' => PHP_EOL,
        ];
    }

    public function collection(Collection $rows)
    {

        echo '-- Update Ongoing Program ' . now() . PHP_EOL;   

        $count=0;
        $updated=0;
        $skipped=0;
        foreach ($rows as $row) {

            $count++;
            if ($row['program_type'] == 'OP' && strlen($row['description']) >= 4 && strtoupper($row['description']) <> 'NONE' )  {
            
                // dd($row);

               $charity = Charity::where('registration_number', $row['bn'])
                ->update(['ongoing_program' => $row['description']]);

               $updated++;

            } else {
//                echo $row['description'] . PHP_EOL ;
                $skipped++;
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }

}
