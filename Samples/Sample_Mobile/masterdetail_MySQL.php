<?php
//todo ## Set the valid path to the file 'INTER-Mediator.php'
require_once(dirname(__FILE__) . '/../../INTER-Mediator.php');

IM_Entry(
    array(
        array(
            'name' => 'placelist',
            'table' => 'not_available',
            'view' => 'postalcode',
            'records' => 1000,
            'maxrecords' => 1000,
            'key' => 'id',
            'navi-control' => 'master-hide-touch',
        ),
        array(
            'name' => 'placedetail',
            'table' => 'not_available',
            'view' => 'postalcode',
            'records' => 1,
            'maxrecords' => 1,
            'key' => 'id',
            'navi-control' => 'detail',
        ),
    ),
    array(
        'credit-including' => 'footer',
    ),
    array(
        'db-class' => 'PDO',
    ),
    //todo ## Set the debug level to false, 1 or 2.
    false
);
