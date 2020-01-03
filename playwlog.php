<?php

////// Author : Tracy Ta  //////

require_once("class.Game.php");

$numberOfGame = 1000;    // number of game that will be tried to play

$numberOfGamePlayed = 0; // number of game actually played

$maxNumCards = 52 * 6;

$numCardPlay = 0;

$noMoreCard = FALSE;

$playerWin = 0;

$logFileName = "./blackjack.log";

// Truncate the file before use it
unlink($logFileName);

//Keeping stat for userhand win
$playerHandWin = array(21=>0, 20=>0, 19=>0, 18=>0, 17=>0, 16=>0);

//////////// Func storing the Stats of winning hands for Player ///////////
function keepWinStat($playerHandValue, $playerHandWin)
{
    switch ($playerHandValue) {
       case 21:
         $playerHandWin[21] += 1;
         break;
       case 20:
         $playerHandWin[20] = $playerHandWin[20] +1;
         break;
       case 19:
         $playerHandWin[19] = $playerHandWin[19] + 1;
         break;
       case 18:       
         $playerHandWin[18] = $playerHandWin[18] + 1;
         break;
       case 17:
         $playerHandWin[17] += 1;
         break;
       case 16:
         $playerHandWin[16] += 1;
         break;
         
    }

    return $playerHandWin;

}

// Initialize the log file 
//file_put_contents($logFileName, "Blackjack log results" . "\n");

for ($idxgame=0; $idxgame < $numberOfGame; $idxgame++) {

    $mygame = new Game();
    $numberOfGamePlayed++;

    file_put_contents($logFileName, "\n", FILE_APPEND | LOCK_EX);

    /////////////// Draw first 2 cards for Player&Dealer hand  ////////////////

    $mygame->PLAYER[0] = $mygame->dealCard();
    $mygame->DEALER[0] = $mygame->dealCard();

    $mygame->PLAYER[1] = $mygame->dealCard();
    $mygame->DEALER[1] = $mygame->dealCard();

    ////////////////////// Draw for Player hand  ///////////////////////

    // if userhand less then 16 keeps drawing until it is more than 16
    $morecard=2;
    while ($mygame->getHandValue($mygame->PLAYER) < 16) {

        $myhandvalue = $mygame->getHandValue($mygame->PLAYER);
       
        if ($myhandvalue > 21 ) {
            break;
        }
        
        if ($numCardPlay == $maxNumCards)
        {
            $noMoreCard = TRUE;
            break; // end game
        }
        $mygame->PLAYER[$morecard++] = $mygame->dealCard();
        $numCardPlay++;
        
    }

    ////////////////////// Draw for Dealer hand  ///////////////////////

    // if dealerhand is smaller than userhand and userhand is not busted, dealer keeps drawing card
    $userhandValue = $mygame->getHandValue($mygame->PLAYER);

    if ( ($userhandValue < 22) ) { 
       $morecard=2;
       while ($mygame->getHandValue($mygame->DEALER) < $userhandValue) {

        $myhandvalue = $mygame->getHandValue($mygame->DEALER);
        
        if ($myhandvalue > 21 ) {
            break;
        }

        if ($numCardPlay == $maxNumCards)
        {
            $noMoreCard = TRUE;
            break; // end game
        }
        $mygame->DEALER[$morecard++] = $mygame->dealCard();
        $numCardPlay++;

       }

    } else {
        $mygame->userhandBusted = TRUE;
    }

    // Break this game loop if no more card is true
    if ( $noMoreCard == TRUE ) {
        break;
    }

    $dealerhandValue = $mygame->getHandValue($mygame->DEALER);
   
    /////////////////////// Check for winning hands //////////////////

    if ($mygame->checkWin($userhandValue, $dealerhandValue, $mygame->userhandBusted) == 1 )
    {
      $mygame->resultstr = "Player wins !";
      $playerWin++;
      $playerHandWin = keepWinStat($userhandValue , $playerHandWin);

    } else if ($mygame->checkWin($userhandValue, $dealerhandValue, $mygame->userhandBusted) == 3 ) 
    {
      $mygame->resultstr = " Its a tie !" ;

    } else { 
      $mygame->resultstr = "Dealer wins !";

    }

    $mytext = "Player : ";

    for ($i = 0; $i < sizeof($mygame->PLAYER); $i++) {

          $mytext = $mytext . $mygame->ToTranslateCard($mygame->PLAYER[$i]);
          if ( $i == sizeof($mygame->PLAYER) - 1 )
             $mytext = $mytext . " = ";
          else
             $mytext = $mytext . " , ";

    }

    $mytext = $mytext . $mygame->getHandValue($mygame->PLAYER) . "\n";
    file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);

    $mytext = "";
    $mytext = "Dealer : ";

    for ($i = 0; $i < sizeof($mygame->DEALER); $i++) {
       
        $mytext = $mytext . $mygame->ToTranslateCard($mygame->DEALER[$i]);
        if ( $i == sizeof($mygame->DEALER) - 1 )
             $mytext = $mytext . " = ";
          else
             $mytext = $mytext . "  , ";
    }
    
    $mytext = $mytext . $mygame->getHandValue($mygame->DEALER) . "\n";
    file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);

    $mytext="";
  
    $mytext = $mytext . "Result : " . $mygame->resultstr . "\n";
    file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);
}

if ( $noMoreCard == TRUE ) {
  $numberOfGamePlayed -= 1;
}

$mytext="";
$mytext = $mytext . "Number of Game " . $numberOfGamePlayed. "\n\n";
file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);

$mytext="";
$mytext = $mytext . "Player Success: " . (($playerWin/ $numberOfGamePlayed) * 0.1) * 1000 .  "%". "\n\n";
file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);

$mytext="";
$mytext = $mytext . "Player Winning Hands => # of times achieved " . "\n";

file_put_contents($logFileName, $mytext , FILE_APPEND | LOCK_EX);

file_put_contents($logFileName, "21 => " . $playerHandWin[21] . "\n", FILE_APPEND | LOCK_EX);
file_put_contents($logFileName, "20 => " . $playerHandWin[20] . "\n", FILE_APPEND | LOCK_EX);
file_put_contents($logFileName, "19 => " . $playerHandWin[19] . "\n", FILE_APPEND | LOCK_EX);
file_put_contents($logFileName, "18 => " . $playerHandWin[18] . "\n", FILE_APPEND | LOCK_EX);
file_put_contents($logFileName, "17 => " . $playerHandWin[17] . "\n", FILE_APPEND | LOCK_EX);
file_put_contents($logFileName, "16 => " . $playerHandWin[16] . "\n", FILE_APPEND | LOCK_EX);

?>
