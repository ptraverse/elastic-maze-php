<?php

use Guzzle\Http\Client;

class Solver
{

	public $maze_guid, $moves, $current_cell;
	public $guzzle, $endpoint; //guzzle object + api endpoint

	public function __construct()
	{
		//POST /api/init - get maze GUID and starting cell		
		$this->guzzle = new Guzzle\Http\Client();
		$this->endpoint = "http://www.epdeveloperchallenge.com/api";
		
		$req = $this->guzzle->createRequest('POST', $this->endpoint."/init");		
		$res = $this->guzzle->send($req);
		$res_a = $res->json(); //array
		
		$this->current_cell = new Cell($res_a);
		$this->maze_guid = $this->current_cell->maze_guid;
		$this->moves = array();
		array_push($this->moves,array(
			"current_x"=>$this->current_cell->x,
			"current_y"=>$this->current_cell->y,
			"moving"=>"START")
		);
	}
	
	public function api_move($direction)
	{
		echo "At (".$this->current_cell->x.",".$this->current_cell->y.") moving ".$direction."\n";
		var_dump($this->current_cell);
		echo "\n\n";
		
		//POST /api/move
		array_push($this->moves,array(
			"current_x"=>$this->current_cell->x,
			"current_y"=>$this->current_cell->y,
			"moving"=>$direction)
		);
		
		$req = $this->guzzle->createRequest('POST', $this->endpoint."/move");
		$q = $req->getQuery();		
		$q->set("mazeGuid", $this->maze_guid);
		$q->set("direction", strtoupper($direction));
		$res = $this->guzzle->send($req);
		$res_a = $res->json();
		$this->current_cell = new Cell($res_a);
	}
	
	public function api_jump($x,$y)
	{
		//POST /api/jump		
	}
	
	public function api_getCurrentCell()
	{
		//GET /api/currentCell
	}
	
	public function solve()
	{
		$nd = '';
		while ($this->current_cell->is_at_end()==FALSE)
		{			
			$nd = $this->current_cell->get_next_direction($nd);
			$this->api_move($nd);
		}
		
		var_dump($this->moves);
		var_dump($this->current_cell);
	}
	
}