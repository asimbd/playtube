<?php

namespace App;

use Common\Files\FileEntry as CommonFileEntry;
use Common\Workspaces\Traits\AttachesToWorkspace;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @method static \Illuminate\Database\Query\Builder|FileEntry onlyStarred()
 * @method static \Illuminate\Database\Query\Builder|FileEntry sharedWithUserOnly($userId)
 */
class FileEntry extends CommonFileEntry
{
    use AttachesToWorkspace;

    protected $table = 'file_entries';

    public function labels(): BelongsToMany
    {
        return $this->tags()->where('tags.type', 'label');
    }

    public function shareableLink(): HasOne
    {
        return $this->hasOne(ShareableLink::class, 'entry_id');
    }

    /**
     * Get only entries that are not children of another entry.
     */
    public function scopeRootOnly(Builder $builder): Builder
    {
        return $builder->where('parent_id', null);
    }

    public function scopeSharedByUser(Builder $builder, int $userId): Builder
    {
        return $builder
            ->whereHas('users', null, '>', 1)
            ->where('owner_id', $userId);
    }

    /**
     * Get only entries that are starred.
     * Only show entries from root or entries whose parent is not starred.
     *
     * @param Builder $builder
     * @return Builder
     */
    public function scopeOnlyStarred(Builder $builder)
    {
        return $builder
            ->whereHas('labels', function ($query) {
                return $query->where('tags.name', 'starred');
            })
            ->where(function ($query) {
                $query
                    ->rootOnly()
                    ->orWhereDoesntHave('parent', function ($query) {
                        return $query->whereHas('labels', function ($q) {
                            return $q->where('tags.name', 'starred');
                        });
                    });
            });
    }

    public function scopeSharedWithUserOnly(
        Builder $query,
        int $userId
    ): Builder {
        // get only entries which user does not own (did not upload)
        return $query
            ->whereNotOwner($userId)
            // get all entries that are in root folder,
            // also get shared entries, whose parent folder is not shared
            // "folder/file.txt", if "file.txt" is shared and "folder" is not shared, get "file.txt"
            ->whereDoesntHave('parent', function (Builder $query) use (
                $userId
            ) {
                return $query->whereNotOwner($userId);
            });
    }

    public function getMorphClass()
    {
        return CommonFileEntry::class;
    }
}
