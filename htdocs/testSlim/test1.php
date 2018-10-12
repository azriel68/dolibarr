<?php

require '../main.inc.php';

require '../contrat/class/contrat.class.php';

$o=new Contrat($db);
$o->fetch(1);

echo $o->id;
var_dump($o->thirdparty->name);
var_dump($o->project->title);
$o->project->contactid = 1;
var_dump($o->project->contactid);
var_dump($o->project->contact->firstname);