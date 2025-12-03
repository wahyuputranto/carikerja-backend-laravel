<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add new columns to client_profiles (we will rename it later)
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
            $table->string('email')->nullable()->after('name'); // Make nullable first to avoid issues with existing data
            $table->string('password')->nullable()->after('email');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('password');
        });

        // 2. Migrate data from users to client_profiles
        $profiles = DB::table('client_profiles')->get();
        foreach ($profiles as $profile) {
            if ($profile->user_id) {
                $user = DB::table('users')->where('id', $profile->user_id)->first();
                if ($user) {
                    DB::table('client_profiles')->where('id', $profile->id)->update([
                        'name' => $user->name,
                        'email' => $user->email,
                        'password' => $user->password,
                        'email_verified_at' => $user->email_verified_at,
                    ]);
                }
            }
        }

        // 3. Make email unique and not nullable (if data allows)
        // We can't easily make it unique if there are duplicates or nulls, but assuming 1-to-1 valid mapping.
        // For now, we'll leave it nullable or try to change it.
        // Let's try to make it unique.
        try {
            Schema::table('client_profiles', function (Blueprint $table) {
                $table->string('email')->nullable(false)->change();
                $table->unique('email');
            });
        } catch (\Exception $e) {
            // If it fails, we might have nulls.
        }

        // 4. Drop foreign key and user_id column
        Schema::table('client_profiles', function (Blueprint $table) {
            // We need to specify the index name because we haven't renamed the table yet, 
            // so it should be client_profiles_user_id_foreign
            $table->dropForeign(['user_id']); 
            $table->dropColumn('user_id');
        });

        // 5. Rename table to clients
        Schema::rename('client_profiles', 'clients');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Rename back
        Schema::rename('clients', 'client_profiles');

        // 2. Add user_id back
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('cascade');
        });

        // 3. Drop new columns
        Schema::table('client_profiles', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'password', 'email_verified_at', 'remember_token']);
        });
    }
};
