<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentImportService
{
    /**
     * Import students from CSV file
     * 
     * CSV Format:
     * Full Name, Programme, Registration Number, Phone Number
     * Example: Kulwa Mangu Majaba,BBICT,MOCU/BBICT/1089/23,0699889430
     * 
     * @param \Illuminate\Http\UploadedFile $file
     * @return array
     */
    public function importFromCSV($file)
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'duplicates' => 0
        ];

        // Validate file
        if (!$file || !$file->isValid()) {
            return [
                'success' => 0,
                'failed' => 0,
                'errors' => ['Invalid file uploaded'],
                'duplicates' => 0
            ];
        }

        $handle = fopen($file->getPathname(), 'r');
        if (!$handle) {
            return [
                'success' => 0,
                'failed' => 0,
                'errors' => ['Could not open file'],
                'duplicates' => 0
            ];
        }

        // Skip header row
        $header = fgetcsv($handle);
        $rowNumber = 2; // Start from row 2 (after header)

        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Validate row data
                $validator = Validator::make([
                    'full_name' => $row[0] ?? null,
                    'programme' => $row[1] ?? null,
                    'registration_number' => $row[2] ?? null,
                    'phone' => $row[3] ?? null,
                ], [
                    'full_name' => 'required|string|max:255',
                    'programme' => 'required|in:BBICT,BHRM,BAT',
                    'registration_number' => 'required|string|max:50',
                    'phone' => 'nullable|string|max:20',
                ]);

                if ($validator->fails()) {
                    $results['errors'][] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                    $results['failed']++;
                    $rowNumber++;
                    continue;
                }

                $fullName = $row[0];
                $programme = $row[1];
                $registrationNumber = $row[2];
                $phone = $row[3] ?? null;

                // Check for duplicate registration number
                if (User::where('username', $registrationNumber)->exists()) {
                    $results['duplicates']++;
                    $results['errors'][] = "Row {$rowNumber}: Registration number '{$registrationNumber}' already exists";
                    $rowNumber++;
                    continue;
                }

                // Parse registration number to extract number and year
                // Format: MOCU/BBICT/1089/23
                $parts = explode('/', $registrationNumber);
                if (count($parts) < 4) {
                    $results['errors'][] = "Row {$rowNumber}: Invalid registration number format. Expected: MOCU/PROGRAM/NUMBER/YEAR";
                    $results['failed']++;
                    $rowNumber++;
                    continue;
                }

                $number = $parts[2] ?? '';
                $year = $parts[3] ?? '';

                // Generate password: mocu.programme.number.year
                $password = 'mocu.' . strtolower($programme) . ".{$number}.{$year}";

                // Create student
                User::create([
                    'name' => $fullName,
                    'username' => $registrationNumber,
                    'email' => null,
                    'role' => 'student',
                    'password' => Hash::make($password),
                    'program' => $programme,
                    'reg_number' => "{$number}/{$year}",
                    'year' => $year,
                    'phone' => $phone,
                    'supervisor_id' => null,
                ]);

                $results['success']++;

            } catch (\Exception $e) {
                $results['errors'][] = "Row {$rowNumber}: " . $e->getMessage();
                $results['failed']++;
            }

            $rowNumber++;
        }

        fclose($handle);

        return $results;
    }

    /**
     * Parse registration number to extract components
     * 
     * @param string $registrationNumber
     * @return array
     */
    private function parseRegistrationNumber($registrationNumber)
    {
        // Format: MOCU/BBICT/1089/23
        $parts = explode('/', $registrationNumber);
        
        return [
            'program' => $parts[1] ?? '',
            'number' => $parts[2] ?? '',
            'year' => $parts[3] ?? '',
        ];
    }
}
