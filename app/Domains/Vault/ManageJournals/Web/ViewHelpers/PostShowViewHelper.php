<?php

namespace App\Domains\Vault\ManageJournals\Web\ViewHelpers;

use App\Helpers\ContactCardHelper;
use App\Helpers\DateHelper;
use App\Helpers\SliceOfLifeHelper;
use App\Models\Contact;
use App\Models\File;
use App\Models\MoodTrackingEvent;
use App\Models\Post;
use App\Models\PostSection;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Collection;

class PostShowViewHelper
{
    public static function data(Post $post, User $user): array
    {
        $sections = self::getSections($post);

        $tags = self::getTags($post);

        $contacts = $post->contacts()
            ->get()
            ->map(fn (Contact $contact) => ContactCardHelper::data($contact));

        $photos = self::getPhotos($post);

        $previousPost = $post->journal
            ->posts()
            ->whereDate('written_at', '<', $post->written_at)
            ->orderBy('written_at', 'desc')
            ->first();

        $nextPost = $post->journal
            ->posts()
            ->whereDate('written_at', '>', $post->written_at)
            ->orderBy('written_at', 'asc')
            ->first();

        $moodTrackingEvents = self::getMood($user, $post);

        return [
            'id' => $post->id,
            'title' => $post->title,
            'title_exists' => $post->title === trans('app.undefined') ? false : true,
            'written_at' => DateHelper::format($post->written_at, $user),
            'published' => $post->published,
            'sections' => $sections,
            'tags' => $tags,
            'contacts' => $contacts,
            'photos' => $photos,
            'moodTrackingEvents' => $moodTrackingEvents,
            'previousPost' => $previousPost ? [
                'id' => $previousPost->id,
                'title' => $previousPost->title,
                'title_exists' => $previousPost->title === trans('app.undefined') ? false : true,
                'url' => [
                    'show' => route('post.show', [
                        'vault' => $post->journal->vault_id,
                        'journal' => $post->journal->id,
                        'post' => $previousPost->id,
                    ]),
                ],
            ] : null,
            'nextPost' => $nextPost ? [
                'id' => $nextPost->id,
                'title' => $nextPost->title,
                'title_exists' => $nextPost->title === trans('app.undefined') ? false : true,
                'url' => [
                    'show' => route('post.show', [
                        'vault' => $post->journal->vault_id,
                        'journal' => $post->journal->id,
                        'post' => $nextPost->id,
                    ]),
                ],
            ] : null,
            'journal' => [
                'name' => $post->journal->name,
                'url' => [
                    'show' => route('journal.show', [
                        'vault' => $post->journal->vault_id,
                        'journal' => $post->journal->id,
                    ]),
                ],
            ],
            'sliceOfLife' => $post->sliceOfLife ? [
                'id' => $post->sliceOfLife->id,
                'name' => $post->sliceOfLife->name,
                'date_range' => SliceOfLifeHelper::getDateRange($post->sliceOfLife),
                'url' => [
                    'show' => route('slices.show', [
                        'vault' => $post->journal->vault_id,
                        'journal' => $post->journal->id,
                        'slice' => $post->sliceOfLife->id,
                    ]),
                ],
            ] : null,
            'url' => [
                'edit' => route('post.edit', [
                    'vault' => $post->journal->vault_id,
                    'journal' => $post->journal->id,
                    'post' => $post->id,
                ]),
                'back' => route('journal.show', [
                    'vault' => $post->journal->vault_id,
                    'journal' => $post->journal->id,
                ]),
            ],
        ];
    }

    private static function getSections(Post $post): Collection
    {
        return $post->postSections()
            ->orderBy('position')
            ->whereNotNull('content')
            ->get()
            ->map(fn (PostSection $section) => [
                'id' => $section->id,
                'label' => $section->label,
                'content' => $section->content,
            ]);
    }

    private static function getTags(Post $post): Collection
    {
        return $post->tags()
            ->orderBy('name')
            ->get()
            ->map(fn (Tag $tag) => [
                'id' => $tag->id,
                'name' => $tag->name,
            ]);
    }

    private static function getPhotos(Post $post): Collection
    {
        return $post->files()
            ->where('type', File::TYPE_PHOTO)
            ->get()
            ->map(fn (File $file) => [
                'id' => $file->id,
                'name' => $file->name,
                'url' => [
                    'display' => 'https://ucarecdn.com/'.$file->uuid.'/-/scale_crop/100x100/smart/-/format/auto/-/quality/smart_retina/',
                ],
            ]);
    }

    private static function getMood(User $user, Post $post): ?Collection
    {
        $contact = $user->getContactInVault($post->journal->vault);

        if (! $contact) {
            return null;
        }

        return MoodTrackingEvent::where('contact_id', $contact->id)
            ->whereDate('rated_at', $post->written_at)
            ->with('moodTrackingParameter')
            ->get()
            ->map(fn (MoodTrackingEvent $mood) => [
                'id' => $mood->id,
                'rating' => $mood->rating,
                'note' => $mood->note,
                'number_of_hours_slept' => $mood->number_of_hours_slept,
                'mood_tracking_parameter' => [
                    'id' => $mood->moodTrackingParameter->id,
                    'label' => $mood->moodTrackingParameter->label,
                ],
            ]);
    }
}
