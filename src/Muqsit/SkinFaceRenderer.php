<?php
/*
*
* Copyright (C) 2017 Muqsit Rayyan
*
* This renderer is derived from FaceLogin's upstream implementation. Most of
* the original face-rendering algorithm was written by Legoboy0215.
* Modified by NhanAZ on 2026-10-15 to validate skin dimensions and cover them
* with isolated synthetic tests.
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
*/

declare(strict_types=1);

namespace Muqsit;

final class SkinFaceRenderer
{
	/** @var list<list<int>> */
	private const RGB_COLORS = [
		[0, 0, 0],
		[0, 0, 170],
		[0, 170, 0],
		[0, 170, 170],
		[170, 0, 0],
		[170, 0, 170],
		[255, 170, 0],
		[170, 170, 170],
		[85, 85, 85],
		[85, 85, 255],
		[85, 255, 85],
		[85, 255, 255],
		[255, 85, 85],
		[255, 85, 255],
		[255, 255, 85],
		[255, 255, 255],
	];

	/** @var list<string> */
	private const FORMAT_CODES = [
		"§0", "§1", "§2", "§3", "§4", "§5", "§6", "§7",
		"§8", "§9", "§a", "§b", "§c", "§d", "§e", "§f",
	];

	public static function isSupportedSkinData(string $skinData) : bool
	{
		return in_array(strlen($skinData), [8192, 16384, 65536], true);
	}

	/**
	 * @param iterable<int|string, mixed> $messages
	 */
	public static function render(string $player, string $skinData, iterable $messages) : ?string
	{
		$skinLength = strlen($skinData);
		if ($skinLength === 8192 || $skinLength === 16384) {
			$faceSize = 8;
			$skinWidth = 64;
			$overlayOffset = 32;
		} elseif ($skinLength === 65536) {
			$faceSize = 16;
			$skinWidth = 128;
			$overlayOffset = 64;
		} else {
			return null;
		}

		$cropLength = $skinWidth * $faceSize * 4;
		$skinPixels = substr($skinData, $cropLength - 4, $cropLength);
		if (strlen($skinPixels) !== $cropLength) {
			return null;
		}

		$symbol = "\xE2\x96\x88";
		$rows = [];
		for ($y = 0; $y < $faceSize; ++$y) {
			$row = "";
			for ($x = 1; $x <= $faceSize; ++$x) {
				$basePixel = (($skinWidth * $y) + $faceSize + $x) * 4;
				$overlayPixel = (($skinWidth * $y) + $faceSize + $x + $overlayOffset) * 4;
				if ($basePixel + 2 >= $cropLength || $overlayPixel + 3 >= $cropLength) {
					return null;
				}

				if (ord($skinPixels[$overlayPixel + 3]) >= 127) {
					$red = ord($skinPixels[$overlayPixel]);
					$green = ord($skinPixels[$overlayPixel + 1]);
					$blue = ord($skinPixels[$overlayPixel + 2]);
				} else {
					$red = ord($skinPixels[$basePixel]);
					$green = ord($skinPixels[$basePixel + 1]);
					$blue = ord($skinPixels[$basePixel + 2]);
				}

				$row .= self::nearestFormat($red, $green, $blue) . $symbol;
			}
			$rows[] = $row;
		}

		foreach ($messages as $line => $message) {
			if (!is_int($line) || $line < 1 || $line > 8 || !is_string($message) || !isset($rows[$line - 1])) {
				continue;
			}
			$rows[$line - 1] .= " " . str_replace("{NAME}", $player, $message);
		}

		return implode("\n", $rows);
	}

	private static function nearestFormat(int $red, int $green, int $blue) : string
	{
		$nearestIndex = 0;
		$nearestDistance = PHP_INT_MAX;
		foreach (self::RGB_COLORS as $index => $color) {
			$redDelta = $red - $color[0];
			$greenDelta = $green - $color[1];
			$blueDelta = $blue - $color[2];
			$distance = ($redDelta * $redDelta) + ($greenDelta * $greenDelta) + ($blueDelta * $blueDelta);
			if ($distance < $nearestDistance) {
				$nearestDistance = $distance;
				$nearestIndex = $index;
			}
		}

		return self::FORMAT_CODES[$nearestIndex];
	}
}
