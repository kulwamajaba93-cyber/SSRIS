<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::createAdmin(
            'System Administrator',
            'admin@mocu.ac.tz',
            'admin123'
        );

        // Create Supervisor User
        $supervisor = User::createSupervisor(
            'Dr. John Smith',
            'supervisor@mocu.ac.tz',
            'supervisor123',
            '0733851614'
        );

        // Create Demo Student
        $student = User::createStudent(
            'Jane Doe',
            'MOCU/BBICT/1089/23',
            '0622848517',
            $supervisor->id
        );

        // Create additional demo students for testing
        $additionalStudents = [
            [
                'name' => 'John Michael',
                'reg_number' => 'MOCU/BBICT/1090/23',
                'phone' => '0712345678',
                'supervisor_id' => $supervisor->id,
            ],
            [
                'name' => 'Sarah Johnson',
                'reg_number' => 'MOCU/BHRM/1091/23',
                'phone' => '0723456789',
                'supervisor_id' => $supervisor->id,
            ],
            [
                'name' => 'Michael Brown',
                'reg_number' => 'MOCU/BAT/1092/23',
                'phone' => '0734567890',
                'supervisor_id' => $supervisor->id,
            ],
            [
                'name' => 'Emily Davis',
                'reg_number' => 'MOCU/BBICT/1093/22',
                'phone' => '0745678901',
                'supervisor_id' => null, // Unassigned student
            ],
            [
                'name' => 'David Wilson',
                'reg_number' => 'MOCU/BHRM/1094/22',
                'phone' => '0756789012',
                'supervisor_id' => null, // Unassigned student
            ],
        ];

        foreach ($additionalStudents as $studentData) {
            User::createStudent(
                $studentData['name'],
                $studentData['reg_number'],
                $studentData['phone'],
                $studentData['supervisor_id']
            );
        }

        // Create additional supervisor for testing
        $supervisor2 = User::createSupervisor(
            'Prof. Mary Johnson',
            'mary.supervisor@mocu.ac.tz',
            'supervisor123',
            '0767890123'
        );

        $this->command->info('Demo data created successfully!');
        $this->command->info('Login Credentials:');
        $this->command->info('Admin: admin@mocu.ac.tz / admin123');
        $this->command->info('Supervisor: supervisor@mocu.ac.tz / supervisor123');
        $this->command->info('Student: MOCU/BBICT/1089/23 / mocu.bbict.1089.23');
        $this->command->info('Additional Supervisor: mary.supervisor@mocu.ac.tz / supervisor123');
    }
}
