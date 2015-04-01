<?php namespace App\KiCad;

class EeschemaComponentObject
{
	const SHAPE_NONE = 0;
	const SHAPE_SQUARE = 1;
	const SHAPE_PIN = 2;
	const SHAPE_ARC = 3;
	const SHAPE_POLYLINE = 4;
	const SHAPE_CIRCLE = 5;
	const SHAPE_TEXT = 6;
	const SHAPE_BEIZER = 7;


	const SHAPE_FILL_NONE = 0;
	const SHAPE_FILLED = 1;
	const SHAPE_FILLED_WITH_BG_BODYCOLOR = 2;

	public $ShapeType = self::SHAPE_NONE;
}
