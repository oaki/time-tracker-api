<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use Nette\Utils\Image;


class PreviewPresenter extends BasePresenter
{


    function actionDefault($id)
    {
        $logImageModel = $this->context->getService('LogImageModel');
        $data = $logImageModel->getImage($id);

        if (!$data) {
            throw new Nette\FileNotFoundException('Image is not found');
        }

        $flag = 5;
        $width = 600;
        $height = 800;


        $image = Image::fromString($data);

        //progressive
        $image->interlace(1);

        if ($flag != 6) {
            $image->resize($width, $height, $flag);
        }

        if ($flag == 5) {
            $image->crop("50%", "50%", $width, $height);
        }

        if ($flag == 6) {
            $image->resize($width, $height, 1);
            $i_height = $image->getHeight();
            $i_width = $image->getWidth();
            $position_top = (int)(($height - $i_height) / 2);
            $position_left = (int)(($width - $i_width) / 2);
            $color = array('red' => 255, 'green' => 255, 'blue' => 255);
            $blank = Image::fromBlank($width, $height, $color);
            $blank->place($image, $position_left, $position_top);
            $image = $blank;
        }

//        $image->save($imageGenerator->getThumbFile($params['type'], $params['src'], $params['ext'], $params['width'], $params['height'], $flag));

        $image->send();
        exit;
    }


}