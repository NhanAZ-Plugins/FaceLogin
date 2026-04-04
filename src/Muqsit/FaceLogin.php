<?php

namespace Muqsit;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;

class FaceLogin extends PluginBase implements Listener {

    private $messages = [];

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        if(!is_dir($dir = $this->getDataFolder())){
            mkdir($dir);
        }
        if(!is_file($dir."messages.yml")){
            file_put_contents($dir."messages.yml", $this->getResource("messages.yml"));
        }

        $messages = [];
        $data = yaml_parse_file($dir."messages.yml");
        $switchToDefault = false;
        if(isset($data["messages"])){
            $messages = $data["messages"];
            foreach($messages as $k => $v){
                if(!is_numeric($k)){
                    $this->getLogger()->critical("Số Dòng Trong messages.yml Phải Là Số Nguyên, $k. Đã Chuyển Sang Tin Nhắn Mặc Định.");
                    $switchToDefault = true;
                    break;
                }elseif($k < 1 || $k > 8){
                    $this->getLogger()->critical("Số Dòng Trong messages.yml Phải Lớn Hơn 0 Và Nhỏ Hơn 9, $k. Đã Chuyển Sang Tin Nhắn Mặc Định.");
                    $switchToDefault = true;
                    break;
                }
            }
        }
        if($switchToDefault){
            $data = yaml_parse_file($this->getFile()."messages.yml");
            $messages = $data["messages"];
        }
        $this->messages = $messages;
    }

    public function sendFace(Player $player, array $messages = null)
    {
        $this->getServer()->getScheduler()->scheduleAsyncTask(new SendPlayerFaceTask($player->getName(), $player->getSkinData(), $messages ?? $this->messages));
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        if($event->getPlayer()->hasPermission("facelogin.show")){
            $this->sendFace($event->getPlayer());
        }
    }
}