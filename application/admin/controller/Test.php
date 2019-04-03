<?php
namespace app\admin\controller;
use think\Controller;
use ensh\pdf;

class Test extends Controller {

      /**
* PDF2PNG
* @param $pdf  待处理的PDF文件
* @param $path 待保存的图片路径
* @param $page 待导出的页面 -1为全部 0为第一页 1为第二页
* @return      保存好的图片路径和文件名
*/
 function pdf2png($pdf='test.pdf',$path='./uploads/test',$page=-1)
{  
	phpinfo();die;
   if(!extension_loaded('imagick'))
   {  
       return false;  
   }  
   if(!file_exists($pdf))
   {  
       return false;  
   }  
   $im = new \Imagick();  
   $im->setResolution(120,120);  
   $im->setCompressionQuality(100);
   if($page==-1)   
      $im->readImage($pdf);
   else
      $im->readImage($pdf."[".$page."]");
   foreach ($im as $Key => $Var)
   {  
       $Var->setImageFormat('png');  
       $filename = $path."/". md5($Key.time()).'.png';
       dump($Var->writeImage($filename));
       if($Var->writeImage($filename) == true)
       {  
           $return[] = $filename;  
       }  
   }  
   $count=count($return);
	for($i=0;$i<$count;$i++)
	{
	   echo "<div align=center><font color=red>Page ".($i+1)."</font><br><img border=3 height=120 width=90 src='".$return[$i]."'></div>";
	}
}  

		 

		 
		/**
		 * 将pdf转化为单一png图片
		 * @param string $pdf  pdf所在路径 （/www/pdf/abc.pdf pdf所在的绝对路径）
		 * @param string $path 新生成图片所在路径 (/www/pngs/)
		 *
		 * @throws Exception
		 */
		function pdf2png2($pdf='test.pdf', $path='./')
		{
			if (!file_exists($pdf)) {
		        echo 2;
		        return false;
		    }
		    try {
		        $im = new \Imagick();
		        $im->setCompressionQuality(100);
		        $im->setResolution(120, 120);//设置分辨率 值越大分辨率越高
		        $im->readImage($pdf);
		 
		        $canvas = new Imagick();
		        $imgNum = $im->getNumberImages();
		        //$canvas->setResolution(120, 120);
		        foreach ($im as $k => $sub) {
		            $sub->setImageFormat('png');
		            //$sub->setResolution(120, 120);
		            $sub->stripImage();
		            $sub->trimImage(0);
		            $width  = $sub->getImageWidth() + 10;
		            $height = $sub->getImageHeight() + 10;
		            if ($k + 1 == $imgNum) {
		                $height += 10;
		            } //最后添加10的height
		            $canvas->newImage($width, $height, new ImagickPixel('white'));
		            $canvas->compositeImage($sub, Imagick::COMPOSITE_DEFAULT, 5, 5);
		        }
		 
		        $canvas->resetIterator();
		        $canvas->appendImages(true)->writeImage($path . microtime(true) . '.png');
		    } catch (Exception $e) {
		        throw $e;
		    }
		}
   
}