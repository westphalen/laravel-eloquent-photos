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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'path',
        'type',
        'size',
    ];

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = [
        'type',
        'size',
        'url',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'size' => 'integer',
    ];

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
        return url('photo/' . $this->publicName());
    }

    /**
     * Get the public name for the Photo.
     *
     * @param bool $extension
     * @return string
     */
    public function publicName($extension = true)
    {
        if ($extension === true) {
            $extension = $this->extension();
        } else if ($extension === false) {
            $extension = '';
        }

        return $this->id . $extension;
    }

    /**
     * Get the photo extension.
     *
     * @return string
     */
    public function extension()
    {
        if (!$this->type) {
            return '';
        }

        return '.' . ($this->type == 'jpeg' ? 'jpg' : $this->type);
    }
}
