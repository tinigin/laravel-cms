<?php

namespace LaravelCms\Http\Controllers;

use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use LaravelCms\Support\ImageHelper;
use LaravelCms\Support\ImageResize;
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
                    $parent = $attachment->getParentModel();
                    if ($parent && method_exists($parent, 'cleanCache')) {
                        $parent->cleanCache();
                    }
                    $attachment->delete();
                    if ($parent && method_exists($parent, 'dependenciesUpdated')) {
                        $parent->dependenciesUpdated();
                    }
                    if ($parent && method_exists($parent, 'updateAttachmentsDependencies')) {
                        $parent->updateAttachmentsDependencies(true);
                    }
                    $status = 'success';
                }
            } catch (\Exception $e) {

            }
        }

        return response()->json(['status' => $status], 200);
    }

    public function sortFiles()
    {
        $status = 'fail';

        if (request()->has('items')) {
            $items = request()->get('items');
            $list = Attachment::whereIn('id', $items)
                ->orderBy('sort', 'asc')
                ->get();

            if ($list) {
                $currentSort = [];
                $items = array_flip($items);

                foreach ($list AS $item) {
                    $currentSort[] = $item->sort;
                }

                foreach ($list AS $item) {
                    $newSortIndex = $currentSort[$items[$item->id]];
                    if ($newSortIndex) {
                        $item->sort = $newSortIndex;
                        $item->save();

                        $parent = $item->getParentModel();
                        if ($parent && method_exists($parent, 'cleanCache')) {
                            $parent->cleanCache();
                        }
                        if ($parent && method_exists($parent, 'dependenciesUpdated')) {
                            $parent->dependenciesUpdated();
                        }
                        if ($parent && method_exists($parent, 'updateAttachmentsDependencies')) {
                            $parent->updateAttachmentsDependencies(true);
                        }
                    }
                }

                $status = 'success';
            }
        }

        return response()->json(['status' => $status], 200);
    }

    public function dataFiles()
    {
        $status = 'fail';

        if (request()->has('id')) {
            $postData = request()->all();
            $item = Attachment::where('id', $postData['id'])->first();
            unset($postData['id']);

            if ($item) {
                $info = $item->additional;
                if (!$info)
                    $info = [];

                foreach ($postData as $key => $value) {
                    $info[$key] = $value;
                }

                $item->additional = $info;
                $item->save();

                $parent = $item->getParentModel();
                if ($parent && method_exists($parent, 'cleanCache')) {
                    $parent->cleanCache();
                }
                if ($parent && method_exists($parent, 'dependenciesUpdated')) {
                    $parent->dependenciesUpdated();
                }
                if ($parent && method_exists($parent, 'updateAttachmentsDependencies')) {
                    $parent->updateAttachmentsDependencies(true);
                }

                $status = 'success';
            }
        }

        return response()->json(['status' => $status], 200);
    }

    public function resizeImage(): JsonResponse
    {
        $status = 'fail';
        $message = 'Произошла ошибка, обратитесь к разработчику сайта';

        if (request()->has(['id', 'coords', 'width', 'height', 'ratio', 'mode', 'thumbnail', 'watermark'])) {
            $file = Attachment::find((int) request()->get('id'));
            $mode = request()->get('mode');
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

                $image = new ImageHelper($tmpFile);
                $image->crop($selectedWidth, $selectedHeight, $x, $y);
                $image->save($tmpFile, quality: 100);

                $image = new ImageHelper($tmpFile);
                $image->contain($finalWidth, $finalHeight, position: 'center');
                $watermark = request()->get('watermark');
                if ($watermark) {
                    $image->watermark($watermark);
                }
                $image->save($tmpFile, quality: 100);

                $thumbnail = request()->get('thumbnail');
                $thumbnailPath = $file->getThumbnailFilename($thumbnail);
                Storage::disk($file->disk)->putFileAs($file->path, $tmpFile, $thumbnailPath, [
                    'mime_type' => $file->mime,
                ]);

                $file->save();

                $parent = $file->getParentModel();
                if ($parent && method_exists($parent, 'cleanCache')) {
                    $parent->cleanCache();
                }
                if ($parent && method_exists($parent, 'dependenciesUpdated')) {
                    $parent->dependenciesUpdated();
                }
                if ($parent && method_exists($parent, 'updateAttachmentsDependencies')) {
                    $parent->updateAttachmentsDependencies(true);
                }

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
