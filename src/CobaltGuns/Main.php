<?php 
namespace CobaltGuns;
 use pocketmine\command\Command;
 use pocketmine\command\CommandSender;
 use pocketmine\command\CommandExecutor;
 use pocketmine\level\particle\DestroyBlockParticle as BloodParticle;
 use pocketmine\level\particle\FlameParticle as WeaponShootParticle;
 use pocketmine\level\sound\AnvilFallSound as DropBombSound;
 use pocketmine\level\sound\BlazeShootSound as WeaponShootSound;
 use pocketmine\level\sound\DoorCrashSound as ExplodeSound;
 use pocketmine\level\Explosion;
 use pocketmine\level\Position;
 use pocketmine\event\Listener;
 use pocketmine\Player;
 use pocketmine\block\Air;
 use pocketmine\block\Block;
 use pocketmine\item\Item;
 use pocketmine\item\Snowball as Bullet;
 use pocketmine\entity\Entity;
 use pocketmine\entity\Snowball;
 use pocketmine\entity\Egg;
 use pocketmine\entity\PrimedTNT;
 use pocketmine\nbt\tag\CompoundTag;
 use pocketmine\nbt\tag\ListTag;
 use pocketmine\nbt\tag\DoubleTag;
 use pocketmine\nbt\tag\FloatTag;
 use pocketmine\event\player\PlayerMoveEvent as PlayerWalkEvent;
 use pocketmine\event\player\PlayerInteractEvent as PlayerUseWeaponEvent;
 use pocketmine\event\player\PlayerItemHeldEvent;
 use pocketmine\event\entity\EntityDamageEvent;
 use pocketmine\event\entity\EntityDamageByEntityEvent;
 use pocketmine\event\entity\EntityDamageByChildEntityEvent;
 use pocketmine\event\entity\ExplosionPrimeEvent;
 use pocketmine\plugin\PluginBase;
 use pocketmine\Server;
 use pocketmine\math\Vector3;
 use pocketmine\inventory\Inventory;
 use pocketmine\utils\TextFormat;
 use pocketmine\utils\Config;
 class Main extends PluginBase implements Listener{ public function onEnable(){ $this->getServer()->getPluginManager()->registerEvents($this, $this);
 $this->getLogger()->info("Enabling CobaltGuns leaked by GeoZDev");
 } public function onExplode(ExplosionPrimeEvent $event){ $event->setBlockBreaking(false);
 } public function onDamage(EntityDamageEvent $event){ if($event instanceof EntityDamageByChildEntityEvent){ $child = $event->getChild();
 if($child instanceof Snowball){ $event->setDamage(6);
 } if($child instanceof Egg){ $event->setDamage(7);
 } if($child->y - $event->getEntity()->y > 1.35){ $event->setDamage(8);
 } } if($event instanceof EntityDamageByEntityEvent){ if($event->getDamager() instanceof Player or $event->getDamager() instanceof Snowball or $event->getDamager() instanceof Egg){ if($event->getEntity() instanceof Player){ if(!$event->isCancelled()){ $event->getEntity()->getLevel()->addParticle(new BloodParticle($event->getEntity(), Block::get(152)));
 } } } } } public function onItemHeld(PlayerItemHeldEvent $event){ $player = $event->getPlayer();
 $item = $player->getInventory()->getItemInHand();
 if($item->getId() == 332){ $player->sendPopup("Bullet");
 } if($player->getInventory()->contains(new Bullet(0, 1))){ if($item->getId() == 290){ $player->sendPopup("�6* �fMachine Gun AK47 �6*\n�eBullet�a/�eSnowball\n�c?\n");
 } if($item->getId() == 291){ $player->sendPopup("�6* �fMiniature Guns �6*\n�eBullet�a/�eSnowball\n�c?\n");
 } if($item->getId() == 292){ $player->sendPopup("�6* �f9MM Gun �6*\n�eBullet�a/�eEgg\n�c?\n");
 } if($item->getId() == 293){ $player->sendPopup("�6* �fSnake Gun �6*\n�eBullet�a/�eSnowball\n�c?\n");
 } if($item->getId() == 359){ $player->sendPopup("�cExplosives");
 } if($item->getId() == 151){ $player->sendPopup("�4Block Blast");
 } } } public function onMove(PlayerWalkEvent $event){ $player = $event->getPlayer();
 $x = $player->x;
 $y = $player->y;
 $z = $player->z;
 $y1 = $y - 1;
 $pos = new Vector3($x, $y1, $z);
 $block = $player->getLevel()->getBlock($pos);
 if($block->getId() == 151){ $explosion = new Explosion(new Position($x, $y + 1, $z, $player->getLevel()), 4);
 $explosion->explodeB();
 $block->getLevel()->setBlock($block, new Air(), true, true);
 $player->getLevel()->addSound(new ExplodeSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 } } public function onShoot(PlayerUseWeaponEvent $event){ $player = $event->getPlayer();
 $level = $player->getLevel();
 $item = $event->getItem();
 $block = $player->getLevel()->getBlock($player->floor()->subtract(0, 1));
 $fdefault = 1.5;
 $nbtdefault = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x ), new DoubleTag( "", $player->y + $player->getEyeHeight () ), new DoubleTag( "", $player->z ) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 if($item->getId() == 290){ if($player->getInventory()->contains(new Bullet(0, 1))){ $bullet = Entity::createEntity("Snowball", $level, $nbtdefault, $player);
 $bullet->setMotion($bullet->getMotion()->multiply($fdefault));
 $bullet->spawnToAll();
 $player->getLevel()->addSound(new WeaponShootSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 $player->getLevel()->addParticle(new WeaponShootParticle(new Vector3($player->x + 0.4, $player->y, $player->z)));
 $player->getInventory()->removeItem(Item::get(ITEM::GUNPOWDER, 0, 1));
 $player->getInventory()->sendContents($player);
 } }elseif($item->getId() == 291){ if($player->getInventory()->contains(new Bullet(0, 1))){ $nbt1 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x + 1), new DoubleTag( "", $player->y + $player->getEyeHeight () ), new DoubleTag( "", $player->z ) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $nbt2 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x - 1), new DoubleTag( "", $player->y + $player->getEyeHeight () ), new DoubleTag( "", $player->z ) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $nbt3 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x), new DoubleTag( "", $player->y + $player->getEyeHeight () ), new DoubleTag( "", $player->z + 1) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $nbt4 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x ), new DoubleTag( "", $player->y + $player->getEyeHeight () ), new DoubleTag( "", $player->z - 1) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $nbt5 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x ), new DoubleTag( "", $player->y + $player->getEyeHeight () + 1), new DoubleTag( "", $player->z ) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $nbt6 = new CompoundTag( "", [ "Pos" => new ListTag( "Pos", [ new DoubleTag( "", $player->x ), new DoubleTag( "", $player->y + $player->getEyeHeight () - 1), new DoubleTag( "", $player->z ) ]), "Motion" => new ListTag( "Motion", [ new DoubleTag( "", - \sin ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "", - \sin ( $player->pitch / 180 * M_PI ) ), new DoubleTag( "",\cos ( $player->yaw / 180 * M_PI ) *\cos ( $player->pitch / 180 * M_PI ) ) ]), "Rotation" => new ListTag( "Rotation", [ new FloatTag( "", $player->yaw ), new FloatTag( "", $player->pitch ) ]) ]);
 $bullet1 = Entity::createEntity("Snowball", $level, $nbt1, $player);
 $bullet2 = Entity::createEntity("Snowball", $level, $nbt2, $player);
 $bullet3 = Entity::createEntity("Snowball", $level, $nbt3, $player);
 $bullet4 = Entity::createEntity("Snowball", $level, $nbt4, $player);
 $bullet5 = Entity::createEntity("Snowball", $level, $nbt5, $player);
 $bullet6 = Entity::createEntity("Snowball", $level, $nbt6, $player);
 $bullet1->setMotion($bullet1->getMotion()->multiply($fdefault));
 $bullet2->setMotion($bullet2->getMotion()->multiply($fdefault));
 $bullet3->setMotion($bullet3->getMotion()->multiply($fdefault));
 $bullet4->setMotion($bullet4->getMotion()->multiply($fdefault));
 $bullet5->setMotion($bullet5->getMotion()->multiply($fdefault));
 $bullet6->setMotion($bullet6->getMotion()->multiply($fdefault));
 $bullet1->spawnToAll();
 $bullet2->spawnToAll();
 $bullet3->spawnToAll();
 $bullet4->spawnToAll();
 $bullet5->spawnToAll();
 $bullet6->spawnToAll();
 $player->getLevel()->addSound(new WeaponShootSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 $player->getLevel()->addParticle(new WeaponShootParticle(new Vector3($player->x + 0.4, $player->y, $player->z)));
 $player->getInventory()->removeItem(Item::get(ITEM::GUNPOWDER, 0, 1));
 $player->getInventory()->sendContents($player);
 } }elseif($item->getId() == 292){ if($player->getInventory()->contains(new Bullet(0, 1))){ $f = 2;
 $bullet = Entity::createEntity("Egg", $level, $nbtdefault, $player);
 $bullet->setMotion($bullet->getMotion()->multiply($f));
 $bullet->spawnToAll();
 $player->getLevel()->addSound(new WeaponShootSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 $player->getLevel()->addParticle(new WeaponShootParticle(new Vector3($player->x + 0.4, $player->y, $player->z)));
 $player->getInventory()->removeItem(Item::get(ITEM::GUNPOWDER, 0, 1));
 $player->getInventory()->sendContents($player);
 } }elseif($item->getId() == 293){ if($player->getInventory()->contains(new Bullet(0, 1))){ $f = 3;
 $bullet = Entity::createEntity("Snowball", $level, $nbtdefault, $player);
 $bullet->setMotion($bullet->getMotion()->multiply($f));
 $bullet->spawnToAll();
 $player->getLevel()->addSound(new WeaponShootSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 $player->getLevel()->addParticle(new WeaponShootParticle(new Vector3($player->x + 0.4, $player->y, $player->z)));
 $player->getInventory()->removeItem(Item::get(ITEM::GUNPOWDER, 0, 1));
 $player->getInventory()->sendContents($player);
 } }elseif($item->getId() == 359){ if($player->getInventory()->contains(new Bullet(0, 5)) && $player->isOp()){ $f = 0.1;
 $tnt = Entity::createEntity("PrimedTNT", $level, $nbtdefault, $player);
 $tnt->setMotion($tnt->getMotion()->multiply($f));
 $tnt->spawnToAll();
 $player->getLevel()->addSound(new DropBombSound(new Vector3($player->x, $player->y, $player->z, $player->getLevel())));
 $player->getLevel()->addParticle(new WeaponShootParticle(new Vector3($player->x + 0.4, $player->y, $player->z)));
 $player->getInventory()->removeItem(Item::get(ITEM::GUNPOWDER, 0, 5));
 $player->getInventory()->sendContents($player);
 } } } }