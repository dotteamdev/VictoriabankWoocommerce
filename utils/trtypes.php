<?php

function getTrTypeName($trTypeNumber) {
    $trtype = '';

    switch ($trTypeNumber) {
        case 0 : $trtype = 'Authorization'; break;
        case 21 : $trtype = 'Sales completion'; break;
        case 24 : $trtype = 'Refund'; break;
    }

    return $trtype;
}