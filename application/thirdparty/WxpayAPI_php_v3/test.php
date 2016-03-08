<?php
/*
php生成文字png图片，可以使用如下方式调用函数:
http://www.yourdomian.com/text_png.php3?msg=helloworld+class&rot=15&size=48&font=fonts/ARIAL.TTF
 */
Header("Content-type: image/png");
class textPNG {
	var $font = 'fonts/TIMES.TTF'; //默认字体. 相对于脚本存放目录的相对路径.
	var $msg = "undefined"; // 默认文字.
	var $size = 24;
	var $rot = 0; // 旋转角度.
	var $pad = 0; // 填充.
	var $transparent = 1; // 文字透明度.
	var $red = 0; // 在黑色背景中...
	var $grn = 0;
	var $blu = 0;
	var $bg_red = 255; // 将文字设置为白色.
	var $bg_grn = 255;
	var $bg_blu = 255;
	function draw() {
		$width = 0;
		$height = 0;
		$offset_x = 0;
		$offset_y = 0;
		$bounds = array();
		$image = "";
// 确定文字高度.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, "W");
		if ($this->rot < 0) {
			$font_height = abs($bounds[7] - $bounds[1]);
		} else if ($this->rot > 0) {
			$font_height = abs($bounds[1] - $bounds[7]);
		} else {
			$font_height = abs($bounds[7] - $bounds[1]);
		}
// 确定边框高度.
		$bounds = ImageTTFBBox($this->size, $this->rot, $this->font, $this->msg);
		if ($this->rot < 0) {
			$width = abs($bounds[4] - $bounds[0]);
			$height = abs($bounds[3] - $bounds[7]);
			$offset_y = $font_height;
			$offset_x = 0;
		} else if ($this->rot > 0) {
			$width = abs($bounds[2] - $bounds[6]);
			$height = abs($bounds[1] - $bounds[5]);
			$offset_y = abs($bounds[7] - $bounds[5]) + $font_height;
			$offset_x = abs($bounds[0] - $bounds[6]);
		} else {
			$width = abs($bounds[4] - $bounds[6]);
			$height = abs($bounds[7] - $bounds[1]);
			$offset_y = $font_height;
			$offset_x = 0;
		}
		$image = imagecreate($width + ($this->pad * 2) + 1, $height + ($this->pad * 2) + 1);
		$background = ImageColorAllocate($image, $this->bg_red, $this->bg_grn, $this->bg_blu);
		$foreground = ImageColorAllocate($image, $this->red, $this->grn, $this->blu);
		if ($this->transparent) {
			ImageColorTransparent($image, $background);
		}

		ImageInterlace($image, false);
// 画图.
		ImageTTFText($image, $this->size, $this->rot, $offset_x + $this->pad, $offset_y + $this->pad, $foreground, $this->font, $this->msg);
// 输出为png格式.
		imagePNG($image);
	}
}

$text = new textPNG;
if (isset($msg)) {
	$text->msg = $msg;
}
// 需要显示的文字
if (isset($font)) {
	$text->font = $font;
}
// 字体
if (isset($size)) {
	$text->size = $size;
}
// 文字大小
if (isset($rot)) {
	$text->rot = $rot;
}
// 旋转角度
if (isset($pad)) {
	$text->pad = $pad;
}
// padding
if (isset($red)) {
	$text->red = $red;
}
// 文字颜色
if (isset($grn)) {
	$text->grn = $grn;
}
// ..
if (isset($blu)) {
	$text->blu = $blu;
}
// ..
if (isset($bg_red)) {
	$text->bg_red = $bg_red;
}
// 背景颜色.
if (isset($bg_grn)) {
	$text->bg_grn = $bg_grn;
}
// ..
if (isset($bg_blu)) {
	$text->bg_blu = $bg_blu;
}
// ..
if (isset($tr)) {
	$text->transparent = $tr;
}
// 透明度 (boolean).
$text->draw();
?>