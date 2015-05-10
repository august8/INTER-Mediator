<?php
/*
 * INTER-Mediator Ver.@@@@2@@@@ Released @@@@1@@@@
 *
 *   Copyright (c) 2010-2015 INTER-Mediator Directive Committee, All rights reserved.
 *
 *   This project started at the end of 2009 by Masayuki Nii  msyk@msyk.net.
 *   INTER-Mediator is supplied under MIT License.
 */
require_once(dirname(__FILE__) . '/../../INTER-Mediator.php');

IM_Entry(
    array(
        array(
            'records' => 10000,
            'name' => 'testtable',
            'sort' => array(
                array('field' => 'dt1', 'direction' => 'desc'),
            ),
            'repeat-control'=>'insert delete',
            'default-values'=>array(
                array('field'=>'dt1', 'value'=>date('Y-m-d H:i:s')),
            ),
            'file-upload' => array(
                array('field'=>'vc1', 'container' => true)
            ),
        ),
        array(
            'name' => 'fileupload',
            'repeat-control'=>'delete',
        ),
    ),
    array(
        'formatter' => array(
            array('field' => 'testtable@dt1', 'converter-class' => 'FMDateTime'),
        ),
        //'authentication' => array(
        //    'user' => array('database_native'),
        //    'storing' => 'cookie-domainwide', // 'cookie'(default), 'cookie-domainwide', 'none'
        //),
    ),
    array('db-class' => 'FileMaker_FX'),
    false
);
