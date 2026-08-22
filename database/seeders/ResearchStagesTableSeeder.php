<?php

namespace Database\Seeders;

use App\Models\ResearchStage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ResearchStagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            ['name' => 'Concept Note', 'step_number' => 1, 'active' => true],
            ['name' => 'Proposal', 'step_number' => 2, 'active' => true],
            ['name' => 'Data Collection & Analysis', 'step_number' => 3, 'active' => true],
            ['name' => 'Report', 'step_number' => 4, 'active' => true],
            ['name' => 'Completed', 'step_number' => 5, 'active' => true],
        ];

        foreach ($stages as $stage) {
            ResearchStage::updateOrCreate(
                ['step_number' => $stage['step_number']],
                $stage
            );
        }
    }
}
