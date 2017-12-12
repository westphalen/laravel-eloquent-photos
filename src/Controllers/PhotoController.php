<?php
/**
 * Created by PhpStorm.
 * User: sune
 * Date: 05/11/2017
 * Time: 12.14
 */

namespace Westphalen\Laravel\Photos\Controllers;

use Illuminate\Contracts\Filesystem\Cloud;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Westphalen\Laravel\Photos\Models\Photo;

class PhotoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param   mixed $id
     * @return  \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show($id)
    {
        if (($extPos = strrpos($id, '.')) !== false) {
            $id = substr($id, 0, $extPos);
        }

        return $this->download($id);
    }

    /**
     * Store a new resource.
     *
     * @param   Request $request
     * @param   FilesystemFactory $filesystem
     * @return  \Illuminate\Http\Response
     * @throws  \Exception
     */
    public function store(Request $request, FilesystemFactory $filesystem)
    {
        if ($data = $request->input('data')) {
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $matches)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($matches[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new HttpException(403, 'Invalid image type: "' . $type . '".');
                }

                $data = base64_decode($data);

                if ($data === false) {
                    throw new HttpException(400, 'Could not decode base64 data for "data:image/' . $type . '".');
                }

                $ext = $type == 'jpeg' ? 'jpg' : $type;
                $filename = $this->generateFileName($ext);
                $path = $this->path($filename);
                if (!$filesystem->disk()->put($path, $data)) {
                    throw new \Exception('Failed to save file to filesystem disk.');
                }

                return $this->storePhoto($filename, $type, $filesystem->disk()->size($path));
            }

            throw new HttpException(400, 'Data must be base64 in format "date:image/TYPE;base64,DATA".');

        } else if ($request->hasFile('file')) {
            $file = $request->file('file');

            $filename = $this->generateFileName($file->extension());
            if (!$file->storeAs($this->path(), $filename)) {
                throw new \Exception('Failed so save file to filesystem disk.');
            }

            return $this->storePhoto(
                $filename,
                ltrim($file->extension(), '.'),
                $filesystem->disk()->size($this->path($filename))
            );
        }

        throw new HttpException(400, 'Please provide either "data" or "file" input.');
    }

    /**
     * Create a photo object for the specified file.
     *
     * @param   string $filename
     * @param   string|null $type
     * @param   integer|null $size
     * @return  \Illuminate\Http\Response
     */
    protected function storePhoto($filename, $type = null, $size = null)
    {
        if ($size === true) {
            $size = filesize($this->path($filename));
        }

        $photo = Photo::create([
            'path' => $filename,
            'type' => $type,
            'size' => $size,
        ]);

        return response($photo, 201)
            ->header('Location', url('photo', [$photo->id]));
    }

    /**
     * Download a photo.
     *
     * @param   string $id
     * @return  \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Exception
     */
    protected function download($id)
    {
        $photo = Photo::findOrFail($id);

        // Attempt to download directly from storage.
        $path = storage_path($this->path($photo->path));
        if (file_exists($path)) {
            return response()->download($path, $photo->publicName());
        }

        // Use local storage driver root path.
        if ($storagePath = config('filesystems.disks.local.root')) {
            $path = $storagePath . '/' . $this->path($photo->path);
            if (file_exists($path)) {
                return response()->download($path, $photo->publicName());
            }
        }

        // Ask Laravel's Storage Disk for url.
        if (($disk = app(FilesystemFactory::class)->disk()) instanceof Cloud) {
            return redirect($disk->url($this->path($photo->path)));
        }

        throw new \Exception('Unable to locate Photo file: "' . $path . '".');
    }

    /**
     * Generate a unique filename.
     *
     * @param   string $ext
     * @param   string|null $path
     * @return  string
     */
    protected function generateFileName($ext, $path = null)
    {
        if ($path === null) {
            $path = $this->path();
        } else {
            $path = rtrim($path, '/') . '/';
        }

        do {
            $filename = Str::random() . '.' . ltrim($ext, '.');
        } while (file_exists($path . $filename));

        return $filename;
    }

    /**
     * Get photo storage path.
     *
     * @param   string|null $file
     * @return  string
     */
    protected function path($file = null)
    {
        return rtrim(config('photo.path', '/'), '/') . '/' . ($file ?: '');
    }
}
