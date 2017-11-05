<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 05/11/2017
 * Time: 12.14
 */

namespace Westphalen\Laravel\Photos\Controllers;

use Illuminate\Routing\Controller;
use Westphalen\Laravel\Photos\Models\Photo;

class PhotoController extends Controller
{
    /**
     * Download a photo.
     *
     * @param string $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($id)
    {
        $photo = Photo::findOrFail($id);

        return response()->download($photo->path, $photo->id . ($photo->ext ? ".{$photo->ext}" : ''));
    }
}
