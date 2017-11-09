<?php
namespace common\helps;
 
class SimpleImageHandle {
 
    var $image;
    var $image_type;

    function load($filename,$is_string = 0) {

		if($is_string == 1)
		{
			$this->image = imagecreatefromstring($filename);
		}else
		{
			$image_info = getimagesize($filename);
			$this->image_type = $image_info[2];
			if( $this->image_type == IMAGETYPE_JPEG ) {
				$this->image = imagecreatefromjpeg($filename);
			} elseif( $this->image_type == IMAGETYPE_GIF ) {
				$this->image = imagecreatefromgif($filename);
			} elseif( $this->image_type == IMAGETYPE_PNG ) {
				$this->image = imagecreatefrompng($filename);
			}
		}
    }
    function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image,$filename,$compression);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image,$filename);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image,$filename);
        }
        if( $permissions != null) {

            chmod($filename,$permissions);
        }
    }
    function output($image_type=IMAGETYPE_JPEG) {

        if( $image_type == IMAGETYPE_JPEG ) {
            imagejpeg($this->image);
        } elseif( $image_type == IMAGETYPE_GIF ) {

            imagegif($this->image);
        } elseif( $image_type == IMAGETYPE_PNG ) {

            imagepng($this->image);
        }
    }

    function getWidth() {

        return imagesx($this->image);
    }

    function getHeight() {

        return imagesy($this->image);
    }
    function resizeToHeight($height) {

        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize($width,$height);
    }

    function resizeToWidth($width) {
        $ratio = $width / $this->getWidth();
        $height = $this->getheight() * $ratio;
        $this->resize($width,$height);
    }

    function scale($scale) {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getheight() * $scale/100;
        $this->resize($width,$height);
    }
 
    function resize($width,$height) {
        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->image = $new_image;
    }

	/*
	*多张图片合并    加文字水印  
	*$imgs:  user_id    用户ID
	*		 nickname   用户昵称
	*		 avatar     用户头像地址
	*        code       推广二维码地址
	*		 dst        背景图片地址
	*/ 
	function mergerImg($imgs) 
	{
		list($max_width, $max_height) = getimagesize($imgs['dst']);
		$dests = imagecreatetruecolor($max_width, $max_height);
 
		$dst_im = imagecreatefromjpeg($imgs['dst']);
 
		imagecopy($dests,$dst_im,0,0,0,0,$max_width,$max_height);
		imagedestroy($dst_im);
		
		//合并头像 
		$this->load($imgs['avatar']);	
		$this->scale(80);		
		imagecopyresized($dests,$this->image,95,15,0,0,80,80,153,153);
		imagedestroy($this->image);

		//合并二维码
		$this->load($imgs['code']);
		$this->scale(70);
		//imagecopy($dests,$code_im,100,300,0,0,150,150);
		imagecopyresized($dests,$this->image,250,880,0,0,250,250,295,295);
//		imagecopyresized($dests,$this->image,250,850,0,0,250,250,295,295);
		imagedestroy($this->image);

		//加水印文字   昵称
		$black = imagecolorallocate($dests,0,0,0);
		imagettftext($dests, 24, 0, 230, 70, $black, dirname(__FILE__)."/../../frontend/web/images/simkai.ttf", $imgs['nickname']);		
		
		imagejpeg($dests,dirname(__FILE__)."/../../frontend/web/images/extendCode/".$imgs['user_id'].'.jpg');
		//释放内存
		imagedestroy($dests);
	}		
}

