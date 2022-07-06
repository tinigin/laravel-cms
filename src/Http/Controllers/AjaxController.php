<?php

namespace LaravelCms\Http\Controllers;

use Gumlet\ImageResize;
use Illuminate\Support\Facades\Storage;
use LaravelCms\Http\Controllers\BaseController;
use LaravelCms\Attachment\Models\Attachment;
use Illuminate\Http\JsonResponse;

class AjaxController extends BaseController
{
    public function removeFile(): JsonResponse
    {
        $status = 'fail';

        if (request()->has('id')) {
            try {
                $attachment = Attachment::findOrFail(request()->get('id'));
                if ($attachment) {
                    $attachment->delete();
                    $status = 'success';
                }
            } catch (\Exception $e) {

            }
        }

        return response()->json(['status' => $status], 200);
    }

    public function resizeImage(): JsonResponse
    {
        $status = 'fail';
        $message = 'Произошла ошибка, обратитесь к разработчику сайта';

        if (request()->has(['id', 'coords', 'width', 'height', 'ratio', 'mode', 'thumbnail'])) {
            $file = Attachment::find((int) request()->get('id'));
            if ($file) {
                $tmpFile = tempnam(sys_get_temp_dir(), $file->physicalPath());
                file_put_contents($tmpFile, Storage::disk($file->disk)->get($file->physicalPath()));

                list($x, $y, $width, $height) = explode(';', request()->get('coords'));
                $ratio = (float) request()->get('ratio');
                $finalWidth = (int) request()->get('width');
                $finalHeight = (int) request()->get('height');

                $x = (float) $x * $ratio;
                $y = (float) $y * $ratio;
                $selectedWidth = (float) $width * $ratio;
                $selectedHeight = (float) $height * $ratio;

                $resizer = new ImageResize($tmpFile);
                $resizer->freecrop($selectedWidth, $selectedHeight, $x, $y);
                $resizer->save($tmpFile);

                $resizer = new ImageResize($tmpFile);
                $resizer->resize($finalWidth, $finalHeight, true);
                $resizer->save($tmpFile);

                $thumbnail = request()->get('thumbnail');
                $thumbnailPath = $file->getThumbnailFilename($thumbnail);
                Storage::disk($file->disk)->putFileAs($file->path, $tmpFile, $thumbnailPath, [
                    'mime_type' => $file->mime,
                ]);

                $file->save();

                $message = 'Изображение обновлено';
                $status = 'success';

            } else {
                $message = 'Изображение с ID = ' . request()->get('id') . ' не найден';
            }

        } else {
            $message = 'Неверные входные данные';
        }

        return response()->json([
            'status' => $status,
            'title' => $message
        ], 200);
    }
}
