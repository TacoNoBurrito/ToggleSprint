<?php
namespace Taco\ToggleSprint;

use pocketmine\Server;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    private $sprinting = [];

    public function onEnable() : void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {
        if ($cmd->getName() == "togglesprint") {
            if ($sender->hasPermission("tsprint.command") or $sender->isOp()) {
                $this->sprintmenu($sender);
                return true;
            }else{
                $sender->sendMessage($this->getServer()->getLanguage()->translateString(TextFormat::RED."%commands.generic.permission"));
                return true;
            }
        }
    }

    public function sprintMenu($player) {
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        if ($api == null or $api->isDisabled()) throw new \Exception("Must Have FormAPI Installed -> Download Here: https://poggit.pmmp.io/p/FormAPI/1.3.0");
        $form = $api->createCustomForm(function (Player $player, array $data = null) {
            if($data === null){
                return true;
            }
            if($data[1] == true){
                if(!isset($this->sprinting[$player->getName()])){
                    $this->sprinting[$player->getName()] = $player->getName();
                    return true;
                } else {
                	return true;
                }
            }
            if($data[1] == false){
                if(isset($this->sprinting[$player->getName()])){
                    unset($this->sprinting[$player->getName()]);
                    return true;
                } else {
                	return true;
                }
            }
        });
        $form->setTitle(TextFormat::BOLD . TextFormat::RED . "Toggle Sprint");
        $form->addLabel(TextFormat::GOLD . "Toggle Sprint Below");
        $form->addToggle("", isset($this->sprinting[$player->getName()]) ? true : false);
        $form->sendToPlayer($player);
        return $form;
    }

    public function onMove(PlayerMoveEvent $event) : void {
        $player = $event->getPlayer();
        if (isset($this->sprinting[$player->getName()])) $player->setSprinting();
    }
}
