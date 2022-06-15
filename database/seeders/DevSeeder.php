<?php

namespace Database\Seeders;

use App\File;
use App\Folder;
use App\User;
use DB;
use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //DB::table('file_entries')->truncate();
        //DB::table('file_entry_models')->truncate();


        //$user = factory(User::class)->create();
        //$this->createChildFolders($user);

        @ini_set("memory_limit", "-1");
        @set_time_limit(0);

        $user = User::find(1);
        $this->createEntries(File::class, 10000, $user);
    }

    private function createEntries(string $type, int $amount = 100, User $user = null)
    {
        $chunks = collect(array_fill(0, $amount, 0))->chunk(500);

        $chunks->each(function() use($user, $type) {
            $files = $type::factory()->count(500)->create([
                'owner_id' => $user->id,
            ]);

            if ($user) {
                $pivot = $files->pluck('id')->combine(array_fill(0, $files->count(), ['owner' => true]));
                $user->entries()->syncWithoutDetaching($pivot);
            }
        });
    }

    private function createChildFolders(User $user) {
        $user->folders()->limit(100)->chunk(200, function($folders) {
            $folders->each(function($folder) {
                $child = Folder::factory()->create(['parent_id' => $folder]);
                $child->generatePath();

                $depth = array_fill(0, 50, 0);
                $lastChild = $child;
                foreach ($depth as $value) {
                    $innerChild = Folder::factory()->create(['parent_id' => $lastChild]);
                    $innerChild->generatePath();
                }
            });
        });
    }
}
