<?php
/*
[license]
[/license]
 */
require "common.inc";
require "common-guest.inc";

//define("NO_TRACE", true);

function yuna_decode( &$file, $fname )
{
	$siz = strlen($file);
	if ( ($siz % 0xa00) == 0 )
		return;

	trace("=== begin sub_6005058() ===\n");
	$dec = "";
	$bylen = 0;
	$bycod = 0;

	$st = 0;
	while ( $st < $siz )
	{
		trace("%6x  %6x  ", $pos, strlen($dec));
		if ( $bylen == 0 )
		{
			$bycod = ord( $file[$st] );
				$st++;
			trace("BYTECODE %2x\n", $bycod);
			$bylen = 8;
			continue;
		}

		$flg = $bycod & 1;
			$bycod >>= 1;
			$bylen--;

		if ( $flg )
		{
			$b1 = $file[$st];
				$st++;
			trace("COPY %2x\n", ord($b1));
			$dec .= $b1;
		}
		else
		{
			$b1 = ord( $file[$st+0] );
			$b2 = ord( $file[$st+1] );
				$st += 2;
			$len = ($b2 >> 3) + 1;
			$pos = (($b2 & 0x07) << 8) | $b1;
			trace("POS  %3d LEN %2d\n", $pos, $len);

			$pos -= 0x800;
			while ( $len > 0 )
			{
				$len--;
				$p = strlen($dec) + $pos;
				$dec .= $dec[$p];
			}
		}
	} // while ( $st < $siz )

	trace("=== end sub_6005058() ===\n");
	file_put_contents("$fname.dec", $dec);
	$file = $dec;
	return;
}

function yuna( $fname )
{
	$file = file_get_contents($fname);
	if ( empty($file) )  return;

	yuna_decode( $file, $fname );

	$rgba = "";
	$siz = strlen($file);
	for ( $i=0; $i < $siz; $i += 2 )
		$rgba .= rgb555( $file[$i+1] . $file[$i+0] );

	$siz = strlen($rgba) / 4;
	$w = 8 * 0x28;
	//$w = 8;
	$h = (int)($siz / $w);
	printf("$fname , %x , $w , $h\n", $siz*4);

	$pix = COPYPIX_DEF();
	$pix['rgba']['w'] = $w;
	$pix['rgba']['h'] = $h;
	$pix['rgba']['pix'] = canvpix($w,$h);

	$pix['src']['w'] = 8;
	$pix['src']['h'] = 8;

	$pos = 0;
	for ( $y=0; $y < $h; $y += 8 )
	{
		for ( $x=0; $x < $w; $x += 8 )
		{
			$b = substr($rgba, $pos, 0x100); // 8*8*4
				$pos += 0x100;

			$pix['src']['pix'] = $b;
			$pix['dx'] = $x;
			$pix['dy'] = $y;

			copypix($pix, 4);
		} // for ( $x=0; $x < $w; $x += 8 )
	} // for ( $y=0; $y < $h; $y += 8 )

	savepix($fname, $pix);
	return;
}

for ( $i=1; $i < $argc; $i++ )
	yuna( $argv[$i] );