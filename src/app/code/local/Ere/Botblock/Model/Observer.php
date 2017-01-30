<?php

use Nabble\SemaltBlocker\Blocker;

class Ere_Botblock_Model_Observer {
    public function BlockerInit($observer){
        try {
           Blocker::protect();
            $blocked = Blocker::blocked();
            if($blocked){
                echo Blocker::explain();
            }
        }catch (Exception $e){
            dd($e->getMessage());
        }
    }
}