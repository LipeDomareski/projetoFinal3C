<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $demo = User::factory()->create([
            'name' => 'Usuário Demo',
            'username' => 'demo',
            'email' => 'demo@carhub.test',
            'bio' => 'Perfil de demonstração do CarHub.',
        ]);

        $users = User::factory(9)->create()->prepend($demo)->values();

        $placeholderSvg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="1200" viewBox="0 0 1200 1200">
  <rect width="1200" height="1200" fill="#e5e7eb"/>
  <rect x="170" y="500" width="860" height="260" rx="60" fill="#111827"/>
  <circle cx="350" cy="790" r="90" fill="#374151"/>
  <circle cx="850" cy="790" r="90" fill="#374151"/>
  <path d="M300 500 430 340h340l130 160" fill="none" stroke="#111827" stroke-width="70" stroke-linejoin="round"/>
  <text x="600" y="1020" text-anchor="middle" font-family="sans-serif" font-size="90" fill="#111827">CarHub</text>
</svg>
SVG;

        $posts = collect();

        foreach ($users->take(6) as $index => $user) {
            // Cada post recebe seu próprio arquivo para que excluir um post
            // não apague a imagem utilizada pelos outros posts do seeder.
            $placeholderPath = 'seed/carhub-placeholder-'.($index + 1).'.svg';
            Storage::disk('public')->put($placeholderPath, $placeholderSvg);

            $posts->push(
                $user->posts()->create([
                    'caption' => 'Post de demonstração #'.($index + 1),
                    'image' => $placeholderPath,
                ])
            );
        }

        foreach ($posts as $index => $post) {
            $commentUser = $users[($index + 1) % $users->count()];

            $post->comments()->create([
                'user_id' => $commentUser->id,
                'content' => 'Comentário de teste no CarHub.',
            ]);

            $likerIds = $users
                ->reject(fn (User $user) => $user->id === $post->user_id)
                ->take(3)
                ->pluck('id')
                ->all();

            $post->likedBy()->syncWithoutDetaching($likerIds);
        }

        foreach ($users as $index => $user) {
            $targetIds = collect([
                $users[($index + 1) % $users->count()]->id,
                $users[($index + 2) % $users->count()]->id,
            ])
                ->reject(fn (int $id) => $id === $user->id)
                ->unique()
                ->values()
                ->all();

            $user->following()->syncWithoutDetaching($targetIds);
        }
    }
}
