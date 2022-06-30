<?php

namespace LaravelCms\Http\Controllers;

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
}
