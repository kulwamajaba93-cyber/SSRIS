<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;

class StudentsImport implements ToCollection, WithHeadingRow
{
    public $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'duplicates' => 0
    ];

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            try {
                // Skip empty rows
                if (empty($row['full_name']) && empty($row['name'])) {
                    continue;
                }

                // Get values from Excel (support both "full_name" and "name" headers)
                $fullName = $row['full_name'] ?? $row['name'] ?? null;
                $programme = $row['programme'] ?? $row['program'] ?? null;
                $registrationNumber = $row['registration_number'] ?? $row['registration_no'] ?? $row['reg_number'] ?? null;
                $phone = $row['phone_number'] ?? $row['phone'] ?? null;

                // Validate required fields
                if (!$fullName || !$programme || !$registrationNumber) {
                    $this->results['errors'][] = "Row: Missing required fields (Full Name, Programme, or Registration Number)";
                    $this->results['failed']++;
                    continue;
                }

                // Validate programme
                if (!in_array(strtoupper($programme), ['BBICT', 'BHRM', 'BAT'])) {
                    $this->results['errors'][] = "Row: Invalid programme '{$programme}'. Must be BBICT, BHRM, or BAT";
                    $this->results['failed']++;
                    continue;
                }

                // Check for duplicate registration number
                if (User::where('username', $registrationNumber)->exists()) {
                    $this->results['duplicates']++;
                    $this->results['errors'][] = "Row: Registration number '{$registrationNumber}' already exists";
                    continue;
                }

                // Use User::createStudent helper method
                User::createStudent(
                    $fullName,
                    $registrationNumber,
                    $phone,
                    null
                );

                $this->results['success']++;

            } catch (\Exception $e) {
                $this->results['errors'][] = "Row: " . $e->getMessage();
                $this->results['failed']++;
            }
        }
    }
}
