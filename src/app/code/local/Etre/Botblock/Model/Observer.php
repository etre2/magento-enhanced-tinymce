<?php

use Nabble\SemaltBlocker\Blocker;

class Etre_Botblock_Model_Observer
{
    public function BlockerInit($observer)
    {
        Blocker::protect();
        $blocked = Blocker::blocked();
        if ($blocked) {
            echo Blocker::explain();
        }
    }
}