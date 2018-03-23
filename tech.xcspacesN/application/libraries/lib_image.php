<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
date_default_timezone_set('Asia/Hong_Kong');

class lib_image{

	//用法: 
	//ImageToJPG('源文件名','目标文件名',目标宽,目标高); 
	//文章源自：烈火网，原文：http://www.veryhuo.com/a/view/36032.html
	function __construct()
	{
		$this->CI = & get_instance();
	}
	
	function ImageCreateFromBMP( $filename ) 
	{ 
		if ( ! $f1 = fopen ( $filename , "rb" )) return FALSE ; 

		$FILE = unpack ( "vfile_type/Vfile_size/Vreserved/Vbitmap_offset" , fread ( $f1 , 14 )); 
		if ( $FILE [ 'file_type' ] != 19778 ) return FALSE ; 

		$BMP = unpack ( 'Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel' . '/Vcompression/Vsize_bitmap/Vhoriz_resolution' . 
		'/Vvert_resolution/Vcolors_used/Vcolors_important' , fread ( $f1 , 40 )); 
		$BMP [ 'colors' ] = pow ( 2 , $BMP [ 'bits_per_pixel' ]); 
		if ( $BMP [ 'size_bitmap' ] == 0 ) $BMP [ 'size_bitmap' ] = $FILE [ 'file_size' ] - $FILE [ 'bitmap_offset' ]; 
		$BMP [ 'bytes_per_pixel' ] = $BMP [ 'bits_per_pixel' ] / 8 ; 
		$BMP [ 'bytes_per_pixel2' ] = ceil ( $BMP [ 'bytes_per_pixel' ]); 
		$BMP [ 'decal' ] = ( $BMP [ 'width' ] * $BMP [ 'bytes_per_pixel' ] / 4 ); 
		$BMP [ 'decal' ] -= floor ( $BMP [ 'width' ] * $BMP [ 'bytes_per_pixel' ] / 4 ); 
		$BMP [ 'decal' ] = 4 - ( 4 * $BMP [ 'decal' ]); 
		if ( $BMP [ 'decal' ] == 4 ) $BMP [ 'decal' ] = 0 ; 

		$PALETTE = array (); 
		if ( $BMP [ 'colors' ] < 16777216 ) 
		{ 
		$PALETTE = unpack ( 'V' . $BMP [ 'colors' ] , fread ( $f1 , $BMP [ 'colors' ] * 4 )); 
		} 

		$IMG = fread ( $f1 , $BMP [ 'size_bitmap' ]); 
		$VIDE = chr ( 0 ); 
		$res = imagecreatetruecolor( $BMP [ 'width' ] , $BMP [ 'height' ]); 
		$P = 0 ; 
		$Y = $BMP [ 'height' ] - 1 ; 
		while ( $Y >= 0 ) 
		{ 
		$X = 0 ; 
		while ( $X < $BMP [ 'width' ]) 
		{ 
		if ( $BMP [ 'bits_per_pixel' ] == 24 ) 
		$COLOR = unpack ( "V" , substr ( $IMG , $P , 3 ) . $VIDE ); 
		elseif ( $BMP [ 'bits_per_pixel' ] == 16 ) 
		{ 
		$COLOR = unpack ( "n" , substr ( $IMG , $P , 2 )); 
		$COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ]; 
		} 
		elseif ( $BMP [ 'bits_per_pixel' ] == 8 ) 
		{ 
		$COLOR = unpack ( "n" , $VIDE . substr ( $IMG , $P , 1 )); 
		$COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ]; 
		} 
		elseif ( $BMP [ 'bits_per_pixel' ] == 4 ) 
		{ 
		$COLOR = unpack ( "n" , $VIDE . substr ( $IMG , floor ( $P ) , 1 )); 
		if (( $P * 2 ) % 2 == 0 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] >> 4 ) ; else $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x0F ); 
		$COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ]; 
		} 
		elseif ( $BMP [ 'bits_per_pixel' ] == 1 ) 
		{ 
		$COLOR = unpack ( "n" , $VIDE . substr ( $IMG , floor ( $P ) , 1 )); 
		if (( $P * 8 ) % 8 == 0 ) $COLOR [ 1 ] = $COLOR [ 1 ] >> 7 ; 
		elseif (( $P * 8 ) % 8 == 1 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x40 ) >> 6 ; 
		elseif (( $P * 8 ) % 8 == 2 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x20 ) >> 5 ; 
		elseif (( $P * 8 ) % 8 == 3 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x10 ) >> 4 ; 
		elseif (( $P * 8 ) % 8 == 4 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x8 ) >> 3 ; 
		elseif (( $P * 8 ) % 8 == 5 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x4 ) >> 2 ; 
		elseif (( $P * 8 ) % 8 == 6 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x2 ) >> 1 ; 
		elseif (( $P * 8 ) % 8 == 7 ) $COLOR [ 1 ] = ( $COLOR [ 1 ] & 0x1 ); 
		$COLOR [ 1 ] = $PALETTE [ $COLOR [ 1 ] + 1 ]; 
		} 
		else 
		return FALSE ; 
		imagesetpixel( $res , $X , $Y , $COLOR [ 1 ]); 
		$X ++ ; 
		$P += $BMP [ 'bytes_per_pixel' ]; 
		} 
		$Y -- ; 
		$P += $BMP [ 'decal' ]; 
		} 

		fclose ( $f1 ); 
		return $res ; 
	}

	function ImageToJPG($srcFile,$dstFile,$towidth,$toheight) 
	{ 
		$quality=80; 
		$data = @GetImageSize($srcFile); 
		switch ($data['2']) 
		{ 
			case 1: 
				$im = imagecreatefromgif($srcFile); 
				break; 
			case 2: 
				$im = imagecreatefromjpeg($srcFile); 
				break; 
			case 3: 
				$im = imagecreatefrompng($srcFile); 
				break; 
			case 6: 
				$im = ImageCreateFromBMP( $srcFile ); 
				break; 
		} 

		// $dstX=$srcW=@ImageSX($im); 

		// $dstY=$srcH=@ImageSY($im); 

		$srcW=@ImageSX($im); 
		$srcH=@ImageSY($im); 
		$dstX=$towidth; 
		$dstY=$toheight; 

		$ni=@imageCreateTrueColor($dstX,$dstY); 

		@ImageCopyResampled($ni,$im,0,0,0,0,$dstX,$dstY,$srcW,$srcH); 
		@ImageJpeg($ni,$dstFile,$quality); 
		@imagedestroy($im); 
		@imagedestroy($ni); 
	} 

}
?>
