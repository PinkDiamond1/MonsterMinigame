<?php
namespace SteamDB\CTowerAttack;

class Lane
{
	/*
	repeated Enemy enemies = 1;
	optional double dps = 2;
	optional double gold_dropped = 3;
	repeated ActiveAbility active_player_abilities = 4;
	repeated uint32 player_hp_buckets = 5;
	optional ETowerAttackElement element = 6;
	// for faster lookup
	optional double active_player_ability_decrease_cooldowns = 7 [default = 1];
	optional double active_player_ability_gold_per_click = 8 [default = 0];
	*/
	public $Players = array();
	public $Enemies;
	public $Dps;
	public $ActivityLog = [];
	private $GoldDropped;
	private $ActivePlayerAbilities;

	public function __construct(
		array $Enemies,
		$Dps,
		$GoldDropped,
		array $ActivePlayerAbilities,
		array $ActivityLog,
		array $PlayerHpBuckets,
		$Element,
		$ActivePlayerAbilityDecreaseCooldowns,
		$ActivePlayerAbilityGoldPerClick
	) {
		$this->Enemies = $Enemies;
		$this->Dps = $Dps;
		$this->GoldDropped = $GoldDropped;
		$this->ActivePlayerAbilities = $ActivePlayerAbilities;
		$this->ActivityLog = $ActivityLog;
		$this->PlayerHpBuckets = $PlayerHpBuckets;
		$this->Element = $Element;
		$this->ActivePlayerAbilityDecreaseCooldowns = $ActivePlayerAbilityDecreaseCooldowns;
		$this->ActivePlayerAbilityGoldPerClick = $ActivePlayerAbilityGoldPerClick;
	}

	public function ToArray()
	{
		return array(
			'enemies' => $this->GetEnemiesArray(),
			'dps' => (double) $this->GetDps(),
			'gold_dropped' => (double) $this->GetGoldDropped(),
			'active_player_abilities' => $this->GetActivePlayerAbilities(),
			'activity_log' => $this->ActivityLog,
			'player_hp_buckets' => $this->GetPlayerHpBuckets(),
			'element' => (int) $this->GetElement(),
			'active_player_ability_decrease_cooldowns' => (double) $this->GetActivePlayerAbilityDecreaseCooldowns(),
			'active_player_ability_gold_per_click' => (double) $this->GetActivePlayerAbilityGoldPerClick()
		);
	}

	public function GetEnemy( $Key )
	{
		if( !isset( $this->Enemies[ $Key ] ) )
		{
			return null;
		}
		
		return $this->Enemies[ $Key ];
	}

	public function GetEnemies()
	{
		return $this->Enemies;
	}

	public function GetEnemiesArray()
	{
		$EnemyArray = array();
		foreach ( $this->GetEnemies() as $Enemy ){
			$EnemyArray[] = $Enemy->ToArray();
		}
		return $EnemyArray;
	}

	public function GetDps()
	{
		return $this->Dps;
	}

	public function GetGoldDropped()
	{
		return $this->GoldDropped;
	}

	public function GetActivePlayerAbilities()
	{
		return $this->ActivePlayerAbilities;
	}

	public function GetPlayerHpBuckets()
	{
		return $this->PlayerHpBuckets;
	}

	public function GetElement()
	{
		return $this->Element;
	}

	public function GetActivePlayerAbilityDecreaseCooldowns()
	{
		return $this->ActivePlayerAbilityDecreaseCooldowns;
	}

	public function GetActivePlayerAbilityGoldPerClick()
	{
		return $this->ActivePlayerAbilityGoldPerClick;
	}

	public function GetPlayers()
	{
		return $this->Players;
	}

	public function AddPlayer( $Player )
	{
		$this->Players[ $Player->GetAccountId() ] = 1;
	}

	public function RemovePlayer( $Player )
	{
		unset( $this->Players[ $Player->GetAccountId() ] );
	}

	public function GiveGoldToPlayers( $Game, $Amount )
	{
		foreach( $this->Players as $AccountId => $Set ) 
		{
			$Player = $Game->GetPlayer( $AccountId );
			$Player->increaseGold( $Amount );
			$Player->Stats->GoldRecieved += $Amount;
		}
	}

	public function GetAliveEnemy()
	{
		foreach( $this->Enemies as $Enemy )
		{
			if( !$Enemy->isDead() )
			{
				return $Enemy;
			}
		}
		return null;
	}

	public function UpdateHpBuckets( $Players )
	{
		$this->PlayerHpBuckets = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
		foreach( $Players as $Player )
		{
			$this->PlayerHpBuckets[ $Player->GetHpLevel() ]++;
		}
	}
}
