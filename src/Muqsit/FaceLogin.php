<?php
/*
*
* Copyright (C) 2017 Muqsit Rayyan
*
*    ___                __             _
*   / __\_ _  ___ ___  / /  ___   __ _(_)_ __
*  / _\/ _` |/ __/ _ \/ /  / _ \ / _` | | '_ \
* / / | (_| | (_|  __/ /__| (_) | (_| | | | | |
* \/   \__,_|\___\___\____/\___/ \__, |_|_| |_|
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
* Modified by NhanAZ on 2026-10-15 for Axolotl-PM API 5 and safe configuration handling.
*
*/
namespace Muqsit;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class FaceLogin extends PluginBase implements Listener {

    /** @var array<int, string> */
    private array $messages = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("messages.yml");
        $config = new Config($this->getDataFolder() . "messages.yml", Config::YAML);
        $rawMessages = $config->get("messages", []);
        if (!is_array($rawMessages)) {
            $this->getLogger()->warning("The messages.yml 'messages' value must be a list keyed from 1 to 8.");
            return;
        }

        foreach ($rawMessages as $line => $message) {
            if (!is_int($line) || $line < 1 || $line > 8 || !is_string($message) || strlen($message) > 512) {
                $this->getLogger()->warning("Ignoring an invalid messages.yml entry; use a string value on lines 1 through 8.");
                continue;
            }
            $this->messages[$line] = $message;
        }
        ksort($this->messages, SORT_NUMERIC);
    }

    /** @param array<int, string>|null $messages */
    public function sendPlayerFace(Player $player, ?array $messages = null): void{
        $skinData = $player->getSkin()->getSkinData();
        if (!SkinFaceRenderer::isSupportedSkinData($skinData)) {
            $this->getLogger()->debug("Skipped the face portrait for a skin with an unsupported image size.");
            return;
        }

        $this->getServer()->getAsyncPool()->submitTask(new SendPlayerFaceTask(
            $player->getName(),
            $skinData,
            $messages ?? $this->messages,
        ));
    }

    public function onJoin(PlayerJoinEvent $event): void {
        $player = $event->getPlayer();
        if (!$player->hasPermission("facelogin.show")) {
            return;
        }
        $this->sendPlayerFace($player);
    }
}
