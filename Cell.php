<?php

//playing with http://www.epdeveloperchallenge.com/

class Cell
{
	
	public $note, $maze_guid, $at_end, $previously_visited, $north, $east, $south, $west, $x, $y;
	
	//using results from GET /api/currentCell
	public function __construct($results_array)
	{			
		$this->note 				= $results_array['currentCell']['note'];
		$this->maze_guid 			= $results_array['currentCell']['mazeGuid'];
		$this->at_end 				= $results_array['currentCell']['atEnd'];
		$this->north 				= $results_array['currentCell']['north'];
		$this->east 				= $results_array['currentCell']['east'];
		$this->west 				= $results_array['currentCell']['west'];
		$this->south 				= $results_array['currentCell']['south'];
		$this->x	 				= $results_array['currentCell']['x'];
		$this->y 					= $results_array['currentCell']['y'];
		$this->previously_visited 	= $results_array['currentCell']['previouslyVisited'];
	}
	
	public function is_at_end()
	{
		return ($this->at_end==TRUE || $this->at_end=="true");
	}
	
	//return direction for next move - either north/east/south/west
	public function get_next_direction($previous_direction)
	{
		$blocked_count = 0;
		$visited_count = 0;
		$unexplored_count = 0;
		
		$directions = array($this->north,$this->east,$this->south,$this->west);
		foreach ($directions as $d)
		{
			if ($d=="UNEXPLORED")
			{
				$unexplored_count++;
			}
			elseif ($d=="VISITED")
			{
				$visited_count++;
			}
			elseif ($d=="BLOCKED")
			{
				$blocked_count++;
			}
		}
		
		
		if ($unexplored_count+$visited_count+$blocked_count!=4)
		{
			throw new Exception("Too many directions!");
		}
		
		if ($blocked_count==4 && $visited_count==0 && $unexplored_count==0)
		{
			throw new Exception("Stuck! Blocked Every Direction.");
		}
		elseif ($blocked_count==3 && $visited_count==1 && $unexplored_count==0)
		{
			return $this->back_one();
		}
		elseif ($blocked_count==3 && $visited_count==0 && $unexplored_count==1)
		{
			return $this->explore_one();
		}
		elseif ($blocked_count==2 && $visited_count==1 && $unexplored_count==1)
		{
			return $this->explore_one(); 
		} 
		elseif ($blocked_count==2 && $visited_count==2 && $unexplored_count==0)
		{
			return $this->back_pick($previous_direction);
		}
		elseif ($blocked_count==1 && $visited_count==3 && $unexplored_count==0)
		{
			return $this->back_pick($previous_direction);
		}
		elseif ($blocked_count==1 && $visited_count==0 && $unexplored_count==3)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==1 && $visited_count==2 && $unexplored_count==1)
		{
			return $this->explore_one();
		}
		elseif ($blocked_count==1 && $visited_count==1 && $unexplored_count==2)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==0 && $visited_count==0 && $unexplored_count==4)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==0 && $visited_count==1 && $unexplored_count==3)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==0 && $visited_count==2 && $unexplored_count==2)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==0 && $visited_count==3 && $unexplored_count==1)
		{
			return $this->explore_pick();
		}
		elseif ($blocked_count==0 && $visited_count==4 && $unexplored_count==0)
		{
			// throw new Exception("Stuck (Visited every direction)!");
			
			// using random picking instead of solving it the "correct" way... kind of cheating
			return $this->explore_pick();
		}
	}
	
	public function back_one($previous_direction)
	{
		if ($this->north=="VISITED")
		{
			return "north";
		}
		
		if ($this->east=="VISITED")
		{
			return "east";
		}
		
		if ($this->south=="VISITED")
		{
			return "south";
		}
		
		if ($this->west=="VISITED")
		{
			return "west";
		}
				
		var_dump($this);
		throw new Exception("Cant Go Back");
				
	}
	
	public function explore_one()
	{
		if ($this->north=="UNEXPLORED")
		{
			$valid_directions[] = "north";
		}
		
		if ($this->east=="UNEXPLORED")
		{
			$valid_directions[] = "east";
		}
		
		if ($this->south=="UNEXPLORED")
		{
			$valid_directions[] = "south";
		}
		
		if ($this->west=="UNEXPLORED")
		{
			$valid_directions[] = "west";
		}
		
		if (count($valid_directions)==0)
		{
			var_dump($this);
			throw new Exception("Cant Explore Any!");
		}
		
		return $valid_directions[array_rand($valid_directions)];
	}
	
	public function back_pick($previous_direction)
	{
		$valid_directions = array();
		if ($this->north=="VISITED" && $previous_direction!=="south")
		{
			$valid_directions[] = "north";
		}
		
		if ($this->east=="VISITED" && $previous_direction!=="west")
		{
			$valid_directions[] = "east";
		}
		
		if ($this->south=="VISITED" && $previous_direction!=="north")
		{
			$valid_directions[] = "south";
		}
		
		if ($this->west=="VISITED" && $previous_direction!=="east")
		{
			$valid_directions[] = "west";
		}
		
		if (count($valid_directions)==0)
		{
			throw new Exception("Cant Go Back");
		}
		
		return $valid_directions[array_rand($valid_directions)];
	}
	
	public function explore_pick()
	{
		return $this->explore_one();
	}
}