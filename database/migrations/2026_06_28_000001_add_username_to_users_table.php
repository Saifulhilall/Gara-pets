<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable()->unique()->after('name');
        });

        DB::table('users')
            ->orderBy('id')
            ->get(['id', 'email', 'name'])
            ->each(function ($user): void {
                $baseUsername = Str::of($user->email ?: $user->name)
                    ->before('@')
                    ->lower()
                    ->replaceMatches('/[^a-z0-9_]+/', '_')
                    ->trim('_')
                    ->limit(40, '')
                    ->value();

                $baseUsername = $baseUsername !== '' ? $baseUsername : 'user';
                $username = $baseUsername;
                $counter = 1;

                while (DB::table('users')
                    ->where('username', $username)
                    ->where('id', '!=', $user->id)
                    ->exists()) {
                    $username = $baseUsername.'_'.$counter;
                    $counter++;
                }

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $username]);
            });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
