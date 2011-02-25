<?php

interface Transmutable {

  public function transmuteTo($agent);
  
  public function transmuteFrom($agent);

}

?>