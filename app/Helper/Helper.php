<?php
namespace App\Helper;

class Helper
{
	public static function jsonReplace($data) {

		$cleaned = str_replace(['["', '"]'], ['', ''], $data);
		$search = ['%5B', '%5D', '%7B', '%7D', '%22', '%2C', '%3A', '%20', '%E2%82%AC'];
		$replace = ['[', ']', '{', '}', '"', ',', ':', ' ', '€'];

		return str_replace($search, $replace, $cleaned);
	}
}