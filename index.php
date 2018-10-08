<?php
header('Content-Type: text/html; charset=utf-8');

require('vendor/autoload.php');
include('parameters.php');


Flight::route('/(@azione)', function ($azione) {
    global $conf, $layer, $menu;
    if ($azione == 'appjs') { //carica la route /appjs
        return true;
    }
    if ($azione === null) {
        $azione = 'singola'; //default all'avvio azione singola
    }
    Flight::render('bootleaf.php', ['conf' => $conf, 'layer' => (object)$layer[$azione], 'menu' => $menu, 'azione' => $azione]);
});

Flight::route('/appjs(/@azione)', function ($azione) {
    global $conf, $layer, $theme, $layerAssociazioni;
    if ($azione === null) {
        $azione = 'singola'; //default all'avvio azione singola
    }


    if ($azione == 'associazioni') {
        $layerToPass = $layerAssociazioni;
    } else {
        $layerToPass = $layer[$azione];
    }

    Flight::render('app.js.php', ['conf' => $conf, 'layer' => $layerToPass, 'theme' => $theme, 'mainLayerSlug' => $conf['mainLayerSlug']]);
});

Flight::start();
