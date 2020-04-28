<?php
require "common.inc";

function saturn_yuna( &$file, $fname )
{
	$w = ordint( $file[1] . $file[0] );
	$h = ordint( $file[3] . $file[2] );
	printf("SATURN , %4d , %4d , $fname\n", $w, $h);

	$ed = strlen($file);
	$st = 4;

	$rgba = "RGBA";
	$rgba .= chrint($w, 4);
	$rgba .= chrint($h, 4);
	while ( $st < $ed )
	{
		$b = ord( $file[$st] );
		if ( $b == 0 || $b & 0x80 )
		{
			$b = $file[$st+1] . $file[$st+0];
			$rgba .= clutpix($b, 0);
			$st += 2;
		}
		else
		{
			$cnt = $b;
			$b = $file[$st+2] . $file[$st+1];
			$t = clutpix($b, 0);
			while ( $cnt )
			{
				$rgba .= $t;
				$cnt--;
			}
			$st += 3;
		}
	}
	$max = 12 + ($w * $h * 4);
	while ( strlen($rgba) < $max )
		$rgba .= ZERO;

	file_put_contents("$fname.rgba", $rgba);
	return;
}

function psx_yuna( &$file, $fname )
{
	$w = str2int($file, 0x10, 2);
	$h = str2int($file, 0x12, 2);
	printf("PSX , %4d , %4d , $fname\n", $w, $h);
	return;

	$ed = strlen($file);
	$st = 0x14;

	$rgba = "";
	//$rgba = "RGBA";
	//$rgba .= chrint($w, 4);
	//$rgba .= chrint($h, 4);
	while ( $st < $ed )
	{
		$b = ord( $file[$st] );
		if ( $b == 0 || $b & 0x80 )
		{
			$b = substr($file, $st+0, 2);
			$rgba .= $b;
			//$rgba .= clutpix($b, 0);
			$st += 2;
		}
		else
		{
			$cnt = $b;
			$b = substr($file, $st+1, 2);
			$t = $b;
			//$t = clutpix($b, 0);
			while ( $cnt )
			{
				$rgba .= $t;
				$cnt--;
			}
			$st += 3;
		}
	}
	file_put_contents("$fname.rgba", $rgba);
	return;
}

function yuna( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	// playstation little endian
	if ( str2int($file, 0, 4) == 0x10 )
		psx_yuna( $file, $fname );
	// saturn big endian
	else
		saturn_yuna( $file, $fname );

	return;
}

for ( $i=1; $i < $argc; $i++ )
	yuna( $argv[$i] );