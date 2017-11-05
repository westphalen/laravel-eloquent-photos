<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 04/11/2017
 * Time: 16.30
 */

namespace Westphalen\Laravel\Photos\Models;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use UuidModelTrait;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'url',
    ];

    /**
     * Accessor for URL attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return url('photo/' . $this->id . ($this->ext ? ".{$this->ext}" : ''));
    }
}
