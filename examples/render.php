<?php

use Lichi\ExcelDrawer\ExcelRender;

require "vendor/autoload.php";

$writer = new XLSXWriter();
$mainHeaderStyles = ['font'=>'Times New Roman','font-size'=>12,'border'=>'left,right,top,bottom', 'halign'=> 'center', 'valign'=> 'center', 'border-style'=>'thin'];
$headerStyles = $mainHeaderStyles + ['wrap_text'=>true, 'widths'=>[5,35,20]];
$bodyStyles = $mainHeaderStyles + ['height'=>17];
$tagStyles = array('wrap_text'=>true, 'height'=>25, 'font'=>'Times New Roman', 'valign'=> 'center', 'font-style'=>'bold','font-size'=>12,'border'=>'none', 'halign'=> 'center');


$header = [
    '№ п/п' => 'string',
    'Наименование показателей'=>'string',
    'Ед.измерения' => 'string',
    'Ед.измеренияe' => 'string',
];

$cardBodyOne = [
    ['Идентификационный №', '', 'fdgdfg'],
];


$a = new ExcelRender($header, $headerStyles);
$a->addBlock('Sheet1', null, $cardBodyOne, $bodyStyles, $tagStyles);
$a->save('test.xlsx');