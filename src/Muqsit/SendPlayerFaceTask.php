<?php
/*
*
* Copyright (C) 2017 Muqsit Rayyan
*
*    ___                __             _
*   / __\_ _  ___ ___  / /  ___   __ _(_)_ __
*  / _\/ _` |/ __/ _ \/ /  / _ \ / _` | | '_ \
* / / | (_| | (_|  __/ /__| (_) | (_| | | | |
* \/   \__,_|\___|\___\____/\___/ \__, |_|_| |_|
*                                |___/
*
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU Lesser General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
*
* @author Muqsit Rayyan
* Twiter: http://twitter.com/muqsitrayyan
* GitHub: http://github.com/Muqsit
* Modified by NhanAZ on 2026-10-15 to validate skin data and use a tested renderer.
*
* Almost everything in this class belongs to Legoboy0215
* Twitter: http://twitter.com/Legoboy0215
* GitHub: http://github.com/legoboy0215
*
* Source: http://gist.github.com/legoboy0215/43282a636844bb0d1accbc91c3fc43f6
*
*/

namespace Muqsit;

use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;
use pmmp\thread\ThreadSafeArray;

class SendPlayerFaceTask extends AsyncTask {

    private string $player;
    private string $skinData;
    /** @var ThreadSafeArray<int, string> */
    private ThreadSafeArray $messages;

    /** @param array<int, string> $messages */
    public function __construct(string $player, string $skinData, array $messages) {
        $this->player = $player;
        $this->skinData = $skinData;
        $this->messages = ThreadSafeArray::fromArray($messages);
    }

    public function onRun(): void {
        $this->setResult(SkinFaceRenderer::render($this->player, $this->skinData, $this->messages) ?? "");
    }

    public function onCompletion(): void {
        $result = $this->getResult();
        if (!is_string($result) || $result === "") {
            return;
        }

        $player = Server::getInstance()->getPlayerExact($this->player);
        if ($player !== null) {
            $player->sendMessage($result);
        }
    }
}
