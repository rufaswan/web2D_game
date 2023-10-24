'use strict';

var QUAD = {};

(function($){

	@@quad-gl.js@@
	@@quad-func.js@@
	@@quad-math.js@@
	@@quad-export.js@@
	@@binary-reader.js@@

	$.version = 'ver 2023-10-2 (beta)';
	$.gl   = new QuadGL  ($);
	$.func = new QuadFunc($);
	$.math = new QuadMath($);
	$.export = new QuadExport($);
	$.binary = new BinaryReader();

})(QUAD);

function QuadData(qlist){
	var $ = this;
	$.LIST = qlist;

	// uploaded files
	$.name = '';
	$.QUAD  = {};
	$.IMAGE = [
		QUAD.gl.createPixel(255) ,
		QUAD.gl.createPixel(255) ,
		QUAD.gl.createPixel(255) ,
		QUAD.gl.createPixel(255) ,
	];
	$.VIDEO = [];

	// activated data
	$.is_wait  = true;
	$.is_flipx = false;
	$.is_flipy = false;
	$.is_draw  = false;
	$.is_hits  = true;
	$.is_lines = true;
	$.zoom   = 1;
	$.matrix = [1,0,0,0 , 0,1,0,0 , 0,0,1,0 , 0,0,0,1];
	$.color  = [1,1,1,1];

	$.attach = {
		type : '',
		id   : 0
	};
	$.anim_fps = 0;
	$.line_index = 0;
	$.prev = ['',-1,-1,true,true];
}