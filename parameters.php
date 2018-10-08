<?php

$popupTemplate = '<table class=\'table table-striped table-bordered table-condensed\'><tr><th>Azione</th><td>" + feature.properties.nome + "</td></tr>"+ "<tr><th>Soggetto promotore</th><td>" + ((\'soggetto promotore\' in feature.properties) ? feature.properties[\'soggetto primotore\'] : \'\') + "</td></tr>"  + "<tr><th>Soggetti</th><td>" + feature.properties.soggetti + "</td></tr>" + "<tr><th>Tipologia</th><td>" + feature.properties.tipologia + "</td></tr>" + "<tr><th>Luoghi</th><td>" + feature.properties.luoghi + "</td></tr>" + "<tr><th>Ambito</th><td>" + feature.properties.ambito + "</td></tr></table>';

$conf = [
    'siteTitle' => 'Coso',
    'mapCenter' => '44.4182, 8.9322',
    'mapZoom' => '14',
    'mainLayerSlug' => 'singola',
    'popupTemplate' => $popupTemplate
];

//first level index
$menu = [];
$menu['singola'] = (object)[
    'name' => 'Azione Singola'
];

//second level index
$theme = [
    'FirstTheme' => "Primo tema"
];

/* first level instance */
$layer['singola'] = [
];

/* first level object */
$layer['singola']['firstObjectIndex'] = (object)[
    'theme' => 'FirstTheme',
    'layerName' => 'firstObjectIndex', /*same as index*/
    'layerDisplayName' => 'First Object Name',
    'slug' => 'firstObjectIndex', /*same as index*/
    'markerImgName' => 'firstMarker.png', /*marker file name*/
    'shortNameProperty' => 'nome', /* which field in geojson */
    'addressProperty' => 'luogo', /* which field in geojson */
    'dataType' => 'geojson',
    'dataPath' => 'data/singola/FirstTheme/firstObjectIndex.geojson', /*same as theme + index*/
    'dataFileName' => 'FirstTheme.geojson', /*same as index*/
    'popupTemplate' => $popupTemplate
];
