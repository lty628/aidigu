<?php
namespace app\common\libs;
use \think\Image;

class ImageHandel
{
    protected $quality = 100;

    public function compressImage($filePath, $saveDir)
    {
        $image = Image::open($filePath);
        $size = $image->size();
        $type = $image->type();
        $mime = $image->mime();
        $width = $size[0];
        $height = $size[1];
        $ratio = $width / $height;

        // 根据尺寸，将图片质量降低
        if ($width > 1920) {
            $this->quality = 80;
        } elseif ($width > 1280) {
            $this->quality = 90;
        }

        // 生成不同尺寸的图片
        $this->generateThumbnails($image, $saveDir, $width, $height);

        return true;
    }

    private function generateThumbnails($image, $saveDir, $width, $height)
    {
        $smallWidth = 50;
        $smallHeight = $smallWidth / ($width / $height);

        $middleWidth = 100;
        $middleHeight = $middleWidth / ($width / $height);

        $bigWidth = 200;
        $bigHeight = $bigWidth / ($width / $height);

        // 生成小尺寸图片
        $smallImage = clone $image;
        $smallImage->thumb($smallWidth, $smallHeight, Image::THUMB_CENTER)->save($saveDir . '/small.jpg', null, $this->quality);

        // 生成中尺寸图片
        $middleImage = clone $image;
        $middleImage->thumb($middleWidth, $middleHeight, Image::THUMB_CENTER)->save($saveDir . '/middle.jpg', null, $this->quality);

        // 生成大尺寸图片
        $bigImage = clone $image;
        $bigImage->thumb($bigWidth, $bigHeight, Image::THUMB_CENTER)->save($saveDir . '/big.jpg', null, $this->quality);
    }
}