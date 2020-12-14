<?php

namespace App\Http\Controllers\V1\Common;

use Illuminate\Support\Str;
use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\V1\Controller;
use App\Http\Requests\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Dingo\Api\Contract\Http\Request;

class ImageController extends Controller
{
    /**
     * 保存图片
     *
     * @param ImageRequest $request
     * @param ImageUploadHandler $uploader
     * @return \Dingo\Api\Http\Response
     */
    public function store(Request $request, ImageUploadHandler $uploader)
    {
        // 图片大小
        $size = $request->type == 'avatar' ? 362 : 1024;
        // 上传图片
        $result = $uploader->save($request->image, Str::plural($request->type), $this->user()->id, $size);
        // 图片类
        $image = new Image();
        // 图片路径
        $image->path = $result['path'];
        // 图片类型
        $image->type = $request->type;
        // 图片使用用户
        $image->user_id = $this->user()->id;
        // 保存图片
        $image->save();
        // 响应信息
        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }

}
