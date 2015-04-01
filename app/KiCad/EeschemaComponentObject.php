<?php namespace App\KiCad;

class EeschemaComponentObject
{
	const SHAPE_NONE = 0;
	const SHAPE_SQUARE = 1;
	const SHAPE_PIN = 2;
	const SHAPE_ARC = 3;

	public $ShapeType = self::SHAPE_NONE;
}
