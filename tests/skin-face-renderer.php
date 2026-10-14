<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/Muqsit/SkinFaceRenderer.php';

use Muqsit\SkinFaceRenderer;

$checkCount = 0;

function expect(bool $condition, string $message) : void
{
	global $checkCount;
	++$checkCount;
	if (!$condition) {
		throw new RuntimeException($message);
	}
}

function putPixel(array &$bytes, int $offset, int $red, int $green, int $blue, int $alpha) : void
{
	$bytes[$offset] = chr($red);
	$bytes[$offset + 1] = chr($green);
	$bytes[$offset + 2] = chr($blue);
	$bytes[$offset + 3] = chr($alpha);
}

expect(SkinFaceRenderer::render('Alex', str_repeat("\0", 4096), []) === null, 'Reject unsupported skin dimensions.');
expect(SkinFaceRenderer::isSupportedSkinData(str_repeat("\0", 8192)), 'Accept legacy 64x32 skin data.');

$messages = SkinFaceRenderer::render('Alex', str_repeat("\0", 16384), [1 => 'Welcome {NAME}', 9 => 'must not render']);
expect($messages !== null, 'Render 64x64 skin data.');
$rows = explode("\n", $messages);
expect(count($rows) === 8, 'Render eight rows for 64x64 skin data.');
expect($rows[0] === str_repeat("§0█", 8) . ' Welcome Alex', 'Render black face pixels and substitute the player name.');
expect(!str_contains($messages, 'must not render'), 'Ignore message lines outside the configured range.');

$bytes = array_fill(0, 16384, "\0");
$cropStart = (64 * 8 * 4) - 4;
$basePixel = $cropStart + (((64 * 0) + 8 + 1) * 4);
$overlayPixel = $cropStart + (((64 * 0) + 8 + 1 + 32) * 4);
putPixel($bytes, $basePixel, 255, 85, 85, 255);
putPixel($bytes, $overlayPixel, 85, 85, 255, 126);
$fallbackResult = SkinFaceRenderer::render('Alex', implode('', $bytes), []);
expect($fallbackResult !== null && str_starts_with($fallbackResult, "§c█"), 'Use base pixels below the overlay alpha threshold.');

putPixel($bytes, $overlayPixel, 85, 85, 255, 127);
$overlayResult = SkinFaceRenderer::render('Alex', implode('', $bytes), []);
expect($overlayResult !== null && str_starts_with($overlayResult, "§9█"), 'Use second-layer pixels at the overlay alpha threshold.');

$wideResult = SkinFaceRenderer::render('Alex', str_repeat("\0", 65536), []);
expect($wideResult !== null && count(explode("\n", $wideResult)) === 16, 'Render 128x128 skins as sixteen rows.');

echo "Skin face renderer tests passed ($checkCount checks).\n";
