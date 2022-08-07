<?php

namespace App\Imports;

use App\Models\Charity;

// use Maatwebsite\Excel\Row;
use Illuminate\Validation\Rule;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithUpserts;


class CharityAltAddressesImport implements  ToModel, WithUpserts, WithHeadingRow, WithStartRow, WithValidation, SkipsOnFailure
// WithValidation, WithEvents, WithBatchInserts, 
{
    use Importable, SkipsFailures;

    protected $updated_at;

    public function __construct()
    {

        $this->updated_at = now();

    }

    /**
     * @return string|array
     */
    public function uniqueBy()
    {
        return 'registration_number';
    }

    public function upsertColumns()
    {
        return [
            'use_alt_address', 'alt_address1', 'alt_address2', 'alt_city', 'alt_province', 'alt_country',
            'alt_postal_code',
            'financial_contact_name', 'financial_contact_title', 'financial_contact_email',
            'comments',
            'updated_by_id'
        ];

    }

    public function model(array $row)
    {

        // correct the postal code if required
        $postal = trim($row['postal']); 
        $expression = '/^([a-zA-Z]\d[a-zA-Z])\ {1}(\d[a-zA-Z]\d)$/';
        $valid = (bool)preg_match($expression, $postal );
        
        if (!$valid) {
            $postal = preg_replace("/\s+/", "", $postal); 
            $postal = implode(' ', str_split($postal, 3));
        }


        return new Charity([
            'registration_number' => $row['pay_acct_no'],
            'use_alt_address' => true,
            'alt_address1' => $row['address_1'],
            'alt_address2' => $row['address_2'],
            'alt_city' => $row['city'],
            'alt_province' => $row['state'],
            'alt_country' => $row['country'],
            'alt_postal_code' => $postal,
            'financial_contact_name' => $row['name'],
            'financial_contact_title' => $row['title'],
            'financial_contact_email' => $row['email'],
            'comments' => $row['comment'],
            'updated_by_id' => 999,
            
        ]);

    }

    public function prepareForValidation($data, $index)
    {
        // get and store the current row for validation purpose
        $this->in_current_row = $data;

        return $data;
    }

    public function rules(): array
    {

        $row = $this->in_current_row;

        return [
            'pay_acct_no' => ['required', Rule::exists('charities', 'registration_number'),                     
                                 Rule::unique('charities','registration_number')
                                    ->where(function ($query) use ($row) {                      
                                        $query->where('updated_at', '>=', $this->updated_at )
                                                ->where('updated_by_id', 999);
                                 }),
            ],

        ];
    }

    // /**
    //  * @return array
    //  */
    public function customValidationMessages()
    {
        return [
            'pay_acct_no.exists' => 'No charity found in the system',
            'pay_acct_no.unique' => 'Duplicated charity found in the xlsx file.',
        ];
    }
    
  
    public function headingRow(): int
    {
        return 2;
    }

    public function startRow(): int
    {
        return 3;
    }

    

}
