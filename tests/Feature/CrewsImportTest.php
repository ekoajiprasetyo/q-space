<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CrewsImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('grade')->nullable();
            $table->string('academic_year')->nullable();
            $table->boolean('is_active')->default(true);
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('class_id')->nullable();
            $table->string('nickname')->nullable();
            $table->string('gender')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function test_teacher_can_import_nicknames_and_gender_from_multiple_classes(): void
    {
        $teacher = User::factory()->create(['role' => 'guru', 'is_active' => true]);

        DB::table('classes')->insert([
            ['id' => 1, 'name' => 'XII-01', 'grade' => '12', 'academic_year' => '2026/2027', 'is_active' => true],
            ['id' => 2, 'name' => 'XII-02', 'grade' => '12', 'academic_year' => '2026/2027', 'is_active' => true],
        ]);
        DB::table('students')->insert([
            ['class_id' => 1, 'nickname' => 'Alya', 'gender' => 'P', 'is_active' => true],
            ['class_id' => 2, 'nickname' => 'Bima', 'gender' => 'L', 'is_active' => true],
            ['class_id' => 2, 'nickname' => 'Tanpa Gender', 'gender' => null, 'is_active' => true],
            ['class_id' => 2, 'nickname' => null, 'gender' => 'L', 'is_active' => true],
        ]);

        $this->actingAs($teacher)
            ->postJson('/crews/import-students', ['class_ids' => [1, 2]])
            ->assertOk()
            ->assertJsonPath('class_names', ['XII-01', 'XII-02'])
            ->assertJsonPath('students.0', ['name' => 'Alya', 'gender' => 'P'])
            ->assertJsonPath('students.1', ['name' => 'Bima', 'gender' => 'L'])
            ->assertJsonPath('students.2', ['name' => 'Tanpa Gender', 'gender' => '']);
    }
}
