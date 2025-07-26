<?php

function getActionName($actionNumber) {
    $action = '';

    switch ($actionNumber) {
        case 0 : $action = 'Zero'; break;
        case 1 : $action = 'One'; break;
        case 2 : $action = 'Two'; break;
        case 3 : $action = 'Three'; break;
    }

    return $action;
}