<?php

require_once 'bootstrap.php';

$airlabs = new AirLabs();
$airlabs->deleteCache('routes');
$airlabs->deleteCache('flights');
$airlabs->deleteCache('airports');

