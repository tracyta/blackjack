<?php 

///// Author : Tracy Ta  /////

class Game
{
	public $DECK = array();
	public $DEALER = array();
	public $PLAYER = array();
	public $cards = array("A","2","3","4","5","6","7","8","9","10","J","Q","K");

        public $NUMOFDECK = 6;

	public $numAces=0;

	public $suits = array("D","H","S","C");
	
	//
	public $resultstr = "";
	public $userhandBusted = FALSE;
	
	public function Game()
	{
		// Create the deck
		$this->createDeck();
		// Shuffle the deck 4 times just to be good and shuffled
		for ($t = 0; $t <= 3; $t++)
		{
			shuffle($this->DECK);
		}
	}
	
	public function SIZEofDeck() 
	{
		return sizeof($this->DECK);
	}

	private function createDeck()
	{
	  for ($numdeck = 0; $numdeck < $this->NUMOFDECK ; $numdeck++)
	    {
		for ($i = 0; $i < 13; $i++)
		{
			// 13x4 = 52 cards
			for($x = 0; $x < 4; $x++)
			{
				// Cycle through suits
				// $cards[$i] = current card type
				// $suits[$x] = current suit
				array_push($this->DECK,$this->cards[$i].$this->suits[$x]);
			}
		}
	  }
	}

	public function dealCard()
	{
		return array_pop($this->DECK);
	}
	

	public function checkWin($uhandValue, $dhandValue, $uhandBusted){
		if ($uhandBusted)
		   return 0;
	
		if($uhandValue > 21){
			/**YOU LOSE**/
		   return 0;
	
		}
		else if ($dhandValue > 21){
			/**YOU WIN**/
		   return 1;
		}
		else if ($uhandValue  == $dhandValue  ){
		   
			/** TIE **/
			return 3;
		}
	
		return 0;
	}

	public function ToTranslateCard($card)
	{
		if (strlen($card) == 3) {
			$face = substr($card,0, 2);
	  
		} else {
			$face = substr($card,0, 1);
	  
		}
		$suit = substr($card,-1,1);
	
		switch($suit)
			{
				case 'C':
					return ($face." of Clubs");

				case 'S':
					return ($face." of Spades");

				case 'H':
					return ($face." of Hearts");

				case 'D':
					return  ($face." of Diamonds");
			}
			
	}
	
	public function getHandValue($cards)
	{
		$value = 0;
		$this->numACES = 0;
		foreach ($cards as &$values)
		{
			$value += $this->getCardValue($values);
			$totalvalue = $value;

			// ACE exists
			if ($this->getCardValue($values) == 1 ) 
			{
				$this->numACES++;
			
				if ($totalvalue <  22 ) {
				   $value += 10; 
			
				}
                
			}
			   
		}

		while ( ($value >= 22 ) && ($this->numACES > 1 )) {

			 $value -= 10;
			 $this->numACES--;
			

		}
		
		return $value;
	}
	
	public function getCardValue($card)
	{
		$face = substr($card,0,-1);
		$suit = substr($card,-1,1);
		$num_pattern = '/[0-9]/';
		$face_pattern = '/[JQK]/';
		if (preg_match($num_pattern,$face))
		{
			// This is a number card
			return $face;
		}
		else if (preg_match($face_pattern,$face))
		{
			// This is a regular face card value of 10
			return 10;
		}
		else
		{
			// Ace 1 or 12
			return 1;
			//print_r ("ACE.");
		}
		//print_r( "Face: " .$face. "Suit: ".$suit);
	}
	
	
}


?>
