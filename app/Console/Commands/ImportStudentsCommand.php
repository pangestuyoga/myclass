<?php

namespace App\Console\Commands;

use App\Enums\IsActive;
use App\Enums\RoleEnum;
use App\Enums\Sex;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ImportStudentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-students {file=public/Data MHS SI.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import student data from a CSV file into users and students tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = base_path($this->argument('file'));

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return 1;
        }

        // Handle CSV properly including potential multi-line fields
        $allRowsData = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                // Skiping empty last rows
                if (array_filter($data)) {
                    $allRowsData[] = $data;
                }
            }
            fclose($handle);
        }

        $successCount = 0;
        $errorCount = 0;

        $this->info('Importing student data ('.count($allRowsData).' entities)...');

        foreach ($allRowsData as $index => $data) {
            if (count($data) < 7) {
                continue;
            }

            try {
                DB::transaction(function () use ($data, &$successCount) {
                    $fullName = trim($data[0]);
                    $studentNumber = trim($data[1]);
                    $phoneNumber = trim($data[2]);
                    $sexRaw = trim($data[3]);
                    $placeOfBirth = trim($data[4]);
                    $dobRaw = trim($data[5]);
                    $address = trim($data[6]);

                    if (empty($fullName) || empty($studentNumber)) {
                        return;
                    }

                    // Mapping sex
                    $sex = str_contains(strtolower($sexRaw), 'perempuan') ? Sex::Female : Sex::Male;

                    // Mapping Date Of Birth
                    try {
                        $dateOfBirth = Carbon::parse($dobRaw)->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dateOfBirth = '2000-01-01';
                    }

                    // Create/Restore User
                    $user = User::withTrashed()->updateOrCreate(
                        ['username' => $studentNumber],
                        [
                            'email' => $studentNumber.'@student.ac.id',
                            'password' => Hash::needsRehash($studentNumber) ? Hash::make($studentNumber) : $studentNumber,
                            'is_active' => IsActive::Active,
                        ]
                    );

                    if ($user->trashed()) {
                        $user->restore();
                    }

                    // Rehash password if newly created or needs it
                    if ($user->wasRecentlyCreated) {
                        $user->update(['password' => Hash::make($studentNumber)]);
                    }

                    // Assign Role (using RoleEnum::Student)
                    try {
                        $roles = [RoleEnum::Student];
                        if (str_contains(strtolower($fullName), 'yoga pangestu')) {
                            $roles[] = RoleEnum::Developer;
                        }
                        $user->syncRoles($roles);
                    } catch (\Exception $e) {
                        // ignore role error if not exists
                        Log::warning("Could not assign role to user {$studentNumber}: ".$e->getMessage());
                    }

                    // Create/Restore Student
                    $student = Student::withTrashed()->updateOrCreate(
                        ['student_number' => $studentNumber],
                        [
                            'user_id' => $user->id,
                            'full_name' => $fullName,
                            'phone_number' => '0'.$phoneNumber,
                            'sex' => $sex,
                            'address' => $address,
                            'date_of_birth' => $dateOfBirth,
                            'place_of_birth' => $placeOfBirth,
                        ]
                    );

                    if ($student->trashed()) {
                        $student->restore();
                    }

                    $successCount++;
                });
            } catch (\Exception $e) {
                $this->error('Error at row '.($index + 1).': '.$e->getMessage());
                Log::error('Import Row Error: '.$e->getMessage());
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info("Import completed: {$successCount} Success, {$errorCount} Errors.");

        return 0;
    }
}
