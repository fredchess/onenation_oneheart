<?php

namespace App\Observers;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogObserver
{
    /**
     * Handle the Blog "created" event.
     */
    public function created(Blog $blog): void
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        $blog->update(["slug" => Str::slug($blog->name)]);

        $blog->update([
            'datas' => [
                'description' => $blog->datas['description'],
                'public_content' => $blog->datas['public_content'],
                'author_id' => $user->id,
                'author' => $user->name,
            ],
        ]);
    }

    /**
     * Handle the Blog "updated" event.
     */
    public function updated(Blog $blog): void
    {
        /**
         * @var User $user
         */
        $user = Auth::user();

        $blog->updateQuietly(["slug" => Str::slug($blog->name)]);

        $blog->updateQuietly([
            'datas' => [
                'description' => $blog->datas['description'],
                'public_content' => $blog->datas['public_content'],
                'author_id' => $user->id,
                'author' => $user->name,
            ],
        ]);
    }

    /**
     * Handle the Blog "deleted" event.
     */
    public function deleted(Blog $blog): void
    {
        //
    }

    /**
     * Handle the Blog "restored" event.
     */
    public function restored(Blog $blog): void
    {
        //
    }

    /**
     * Handle the Blog "force deleted" event.
     */
    public function forceDeleted(Blog $blog): void
    {
        //
    }
}
