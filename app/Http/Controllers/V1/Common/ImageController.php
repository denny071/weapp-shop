<?php

namespace App\Http\Controllers\V1\Common;

use Illuminate\Support\Str;
use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\V1\Controller;
use App\Http\Requests\ImageRequest;
use App\Models\Image;
use App\Models\User;
use App\Transformers\ImageTransformer;
use Dingo\Api\Contract\Http\Request;

class ImageController extends Controller
{
    /**
     * 上传头像
     *
     * @param ImageRequest $request
     * @param ImageUploadHandler $uploader
     * @return \Dingo\Api\Http\Response
     */
    public function avatar(Request $request, ImageUploadHandler $uploader)
    {
        // 上传图片
        $result = $uploader->save($request->image, "avatar", $this->user()->id, 362);

        User::where("id",$this->user()->id)->update(["avatar" =>  $result['path']]);
        // 响应信息
        return $this->response->created();
    }

}
