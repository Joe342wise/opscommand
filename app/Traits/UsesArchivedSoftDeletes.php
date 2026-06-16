<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;

trait UsesArchivedSoftDeletes
{
    use SoftDeletes;

    public const DELETED_AT = 'archived_at';
}
