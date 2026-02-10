<?php

declare(strict_types=1);

/**
 * 2026 DeveMain
 *
 * All rights reserved. For internal use only.
 * Unauthorized copying, modification, or distribution is prohibited.
 *
 * @author    DeveMain <devemain@gmail.com>
 * @copyright 2026 DeveMain
 * @license   PROPRIETARY
 *
 * @link      https://github.com/DeveMain
 */

namespace App\Models;

use App\Models\Traits\HasContentHashTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static safeUpsert(array $values, $uniqueBy, $update = null)
 */
class Fact extends Model
{
    use HasContentHashTrait;

    protected $visible = [
        'id', 'content', 'views', 'created_at',
    ];
}
