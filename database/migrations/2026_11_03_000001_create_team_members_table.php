<?php

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('bio')->nullable();
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter')->nullable();
            $table->string('whatsapp')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Reprend les membres de l'équipe affichés jusqu'ici : utilisateurs avec datas.visible = 1,
        // hors responsables d'orphelinats (eux aussi marqués visibles)
        $hasRoles = Schema::hasTable('roles') && Schema::hasTable('model_has_roles');

        User::query()->orderBy('id')->get()
            ->filter(fn (User $user) => ($user->datas['visible'] ?? 0) == 1)
            ->reject(fn (User $user) => $hasRoles && $user->hasRole('responsable'))
            ->values()
            ->each(function (User $user, int $index) {
                $member = TeamMember::create([
                    'name' => $user->name,
                    'role' => $user->datas['title'] ?? null,
                    'bio' => $user->datas['description'] ?? null,
                    'facebook' => $user->datas['facebook'] ?? null,
                    'instagram' => $user->datas['instagram'] ?? null,
                    'linkedin' => $user->datas['linkedin'] ?? null,
                    'twitter' => $user->datas['twitter'] ?? null,
                    'whatsapp' => $user->datas['whatsapp'] ?? null,
                    'is_visible' => true,
                    'sort_order' => $index + 1,
                ]);

                try {
                    $user->getFirstMedia()?->copy($member, 'photo');
                } catch (\Throwable $e) {
                    Log::warning("Team member photo not copied for user {$user->id}: {$e->getMessage()}");
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
