<?php  ?>
var map, featureList;

<?php foreach ($layer as $l) {
    echo "var ".$l->slug."Search = [];\n";
}
?>

$(window).resize(function() {
  sizeLayerControl();
});

$(document).on("click", ".feature-row", function(e) {
  $(document).off("mouseout", ".feature-row", clearHighlight);
  sidebarClick(parseInt($(this).attr("id"), 10));
});

if ( !("ontouchstart" in window) ) {
  $(document).on("mouseover", ".feature-row", function(e) {
    highlight.clearLayers().addLayer(L.circleMarker([$(this).attr("lat"), $(this).attr("lng")], highlightStyle));
  });
}

$(document).on("mouseout", ".feature-row", clearHighlight);

$("#about-btn").click(function() {
  $("#aboutModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});
/*
$("#full-extent-btn").click(function() {
  map.fitBounds(<?php echo $conf['mainLayerSlug']; ?>.getBounds());
  $(".navbar-collapse.in").collapse("hide");
  return false;
});
*/

$("#legend-btn").click(function() {
  $("#legendModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#login-btn").click(function() {
  $("#loginModal").modal("show");
  $(".navbar-collapse.in").collapse("hide");
  return false;
});

$("#list-btn").click(function() {
  animateSidebar();
  return false;
});

$("#nav-btn").click(function() {
  $(".navbar-collapse").collapse("toggle");
  return false;
});

$("#sidebar-toggle-btn").click(function() {
  animateSidebar();
  return false;
});

$("#sidebar-hide-btn").click(function() {
  animateSidebar();
  return false;
});

function animateSidebar() {
  $("#sidebar").animate({
    width: "toggle"
  }, 350, function() {
    map.invalidateSize();
  });
}

function sizeLayerControl() {
  $(".leaflet-control-layers").css("max-height", $("#map").height() - 50);
}

function clearHighlight() {
  highlight.clearLayers();
}

function sidebarClick(id) {
  var layer = markerClusters.getLayer(id);
  map.setView([layer.getLatLng().lat, layer.getLatLng().lng], 17);
  layer.fire("click");
  /* Hide sidebar and go to the map on small screens */
  if (document.body.clientWidth <= 767) {
    $("#sidebar").hide();
    map.invalidateSize();
  }
}

function syncSidebar() {
  /* Empty sidebar features */
  $("#feature-list tbody").empty();

<?php foreach ($layer as $l) : ?>
  <?php echo $l->slug?>.eachLayer(function (layer) {
    if (map.hasLayer(<?php echo $l->slug?>Layer)) {
      if (map.getBounds().contains(layer.getLatLng())) {
        var appendString = '<tr class="feature-row" id="' + L.stamp(layer) + '" lat="' + layer.getLatLng().lat + '" lng="' + layer.getLatLng().lng;
        appendString +='"><td style="vertical-align: middle;"><img width="16" height="18" src="assets/img/<?php echo $l->markerImgName;?>"></td><td class="feature-name">';
        appendString +=layer.feature.properties.<?php echo $l->shortNameProperty; ?> + '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>'
        $("#feature-list tbody").append(appendString);
      }
    }
  });
<?php endforeach; ?>

  /* Update list.js featureList */
  featureList = new List("features", {
    valueNames: ["feature-name"]
  });
  featureList.sort("feature-name", {
    order: "asc"
  });
}

/* basemap */
var osmBaseMap = L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
	attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
	subdomains: 'abcd',
	maxZoom: 19
});
/*
var osmBaseMap = L.tileLayer("https://{s}.tile.osm.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
});
*/

var highlight = L.geoJson(null);
var highlightStyle = {
  stroke: false,
  fillColor: "#00FFFF",
  fillOpacity: 0.7,
  radius: 10
};

/* Single marker cluster layer to hold all clusters */
var markerClusters = new L.MarkerClusterGroup({
  spiderfyOnMaxZoom: true,
  showCoverageOnHover: false,
  zoomToBoundsOnClick: true,
  disableClusteringAtZoom: 16
});

/* geojson layer configuration */
<?php foreach ($layer as $l) : ?>
var <?php echo $l->slug;?>Layer = L.geoJson(null);
var <?php echo $l->slug;?> = L.geoJson(null, {
  pointToLayer: function (feature, latlng) {
    return L.marker(latlng, {
      icon: L.icon({
        iconUrl: "assets/img/<?php echo $l->markerImgName;?>",
        iconSize: [24, 28],
        iconAnchor: [12, 28],
        popupAnchor: [0, -25]
      }),
      title: feature.properties.<?php echo $l->shortNameProperty;?>,
      riseOnHover: true
    });
  },
  onEachFeature: function (feature, layer) {
    if (feature.properties) {
        <?php
        $imageUrl = 'pictures/'.$l->slug.'.jpg';
        if (!file_exists(dirname(dirname(__FILE__)).'/'.$imageUrl)) {
            $imageUrl="http://placehold.it/500x350";
        }
        ?>
      var image = '<img src=\'<?php echo $imageUrl ?>\' style=\'height:250px;text-align:center;display:block;margin:auto;\'/>';


      var content = image;
          content = content + "<br/><?php echo $conf['popupTemplate'] ?>";


      layer.on({
        click: function (e) {
          $("#feature-title").html(feature.properties.<?php echo $l->shortNameProperty;?>);
          $("#feature-info").html(content);
          $("#featureModal").modal("show");
          highlight.clearLayers().addLayer(L.circleMarker([feature.geometry.coordinates[1], feature.geometry.coordinates[0]], highlightStyle));
        }
      });

      var appendFeature = '<tr class="feature-row" id="' + L.stamp(layer) + '" lat="' + layer.getLatLng().lat + '" lng="' + layer.getLatLng().lng;
      appendFeature+= '"><td style="vertical-align: middle;"><img width="16" height="18" src="assets/img/<?php echo $l->markerImgName;?>"></td><td class="feature-name">';
      appendFeature+= layer.feature.properties.<?php echo $l->shortNameProperty;?> + '</td><td style="vertical-align: middle;"><i class="fa fa-chevron-right pull-right"></i></td></tr>';
      $("#feature-list tbody").append(appendFeature);
      <?php echo $l->slug;?>Search.push({
        name: layer.feature.properties.<?php echo $l->shortNameProperty;?>,
        address: layer.feature.properties.<?php echo $l->addressProperty;?>,
        source: "<?php echo $l->layerName;?>",
        id: L.stamp(layer),
        lat: layer.feature.geometry.coordinates[1],
        lng: layer.feature.geometry.coordinates[0]
      });
    }
  }
});
$.getJSON("<?php echo $l->dataPath;?>", function (data) {
  <?php echo $l->slug;?>.addData(data);
  map.addLayer(<?php echo $l->slug;?>Layer);
});
<?php endforeach; ?>

map = L.map("map", {
  zoom: <?php echo $conf['mapZoom'];?>,
  center: [<?php echo $conf['mapCenter'];?>],
  layers: [osmBaseMap, markerClusters, highlight],
  zoomControl: false,
  attributionControl: false
});

/* Layer control listeners that allow for a single markerClusters layer */
map.on("overlayadd", function(e) {
<?php foreach ($layer as $l) : ?>
  if (e.layer === <?php echo $l->slug;?>Layer) {
    markerClusters.addLayer(<?php echo $l->slug;?>);
    syncSidebar();
  }
<?php endforeach; ?>
});

map.on("overlayremove", function(e) {
<?php foreach ($layer as $l) : ?>
  if (e.layer === <?php echo $l->slug;?>Layer) {
    markerClusters.removeLayer(<?php echo $l->slug;?>);
    syncSidebar();
  }
<?php endforeach; ?>
});


/* Filter sidebar feature list to only show features in current map bounds */
map.on("moveend", function (e) {
  syncSidebar();
});

/* Clear feature highlight when map is clicked */
map.on("click", function(e) {
  highlight.clearLayers();
});

/* Attribution control */
function updateAttribution(e) {
  $.each(map._layers, function(index, layer) {
    if (layer.getAttribution) {
      $("#attribution").html((layer.getAttribution()));
    }
  });
}
map.on("layeradd", updateAttribution);
map.on("layerremove", updateAttribution);

var attributionControl = L.control({
  position: "bottomright"
});
attributionControl.onAdd = function (map) {
  var div = L.DomUtil.create("div", "leaflet-control-attribution");
  div.innerHTML = "<span class='hidden-xs'>Developed by <a href='http://bryanmcbride.com'>bryanmcbride.com</a> | </span><a href='#' onclick='$(\"#attributionModal\").modal(\"show\"); return false;'>Attribution</a>";
  return div;
};
map.addControl(attributionControl);

/*
var zoomControl = L.control.zoom({
  position: "bottomright"
}).addTo(map);
*/
var zoomHome = L.Control.zoomHome({position: 'bottomright'});
zoomHome.addTo(map);

/* GPS enabled geolocation control set to follow the user's location */
var locateControl = L.control.locate({
  position: "bottomright",
  drawCircle: true,
  follow: true,
  setView: true,
  keepCurrentZoomLevel: true,
  markerStyle: {
    weight: 1,
    opacity: 0.8,
    fillOpacity: 0.8
  },
  circleStyle: {
    weight: 1,
    clickable: false
  },
  icon: "fa fa-location-arrow",
  metric: false,
  strings: {
    title: "La mia posizione",
    popup: "Sei entro {distance} {unit} da questo punto",
    outsideMapBoundsMsg: "Sembri posizionato al di fuori dei confini della mappa"
  },
  locateOptions: {
    maxZoom: 18,
    watch: true,
    enableHighAccuracy: true,
    maximumAge: 10000,
    timeout: 10000
  }
}).addTo(map);


/* Larger screens get expanded layer control and visible sidebar */
if (document.body.clientWidth <= 767) {
  var isCollapsed = true;
} else {
  var isCollapsed = false;
}

var baseLayers = {
  "Mappa": osmBaseMap
};

var groupedOverlays = {
<?php foreach ($theme as $tk => $t) {

    ?>
  "<?php echo $t?>": {
<?php

foreach ($layer as $l) {
    if ($l->theme != $tk) {
        continue;
    }
?>
    "<img src='assets/img/<?php echo $l->markerImgName;?>' width='24' height='28'>&nbsp;<?php echo $l->layerDisplayName;?>": <?php echo $l->slug;?>Layer,
<?php } ?>
  },
<?php } ?>
};

L.Control.GroupedLayers.include({
    addOverlays: function () {
        for (var i in this._layers) {
            if (this._layers[i].overlay) {
                if (!this._map.hasLayer(this._layers[i].layer)) {
                    this._map.addLayer(this._layers[i].layer);
                }
            }
        }
    },
    removeOverlays: function () {
        for (var i in this._layers) {
            if (this._layers[i].overlay) {
                if (this._map.hasLayer(this._layers[i].layer)) {
                    this._map.removeLayer(this._layers[i].layer);
                }
            }
        }
    }
});

var layerControl = L.control.groupedLayers(baseLayers, groupedOverlays, {
  collapsed: isCollapsed
}).addTo(map);

/* Highlight search box text on click */
$("#searchbox").click(function () {
  $(this).select();
});

/* Prevent hitting enter from refreshing the page */
$("#searchbox").keypress(function (e) {
  if (e.which == 13) {
    e.preventDefault();
  }
});

$("#featureModal").on("hidden.bs.modal", function (e) {
  $(document).on("mouseout", ".feature-row", clearHighlight);
});

/* Typeahead search functionality */
$(document).one("ajaxStop", function () {
  $("#loading").hide();
  sizeLayerControl();
  /* Fit map to main layer bounds */
  /*map.fitBounds(<?php echo $mainLayerSlug; ?>.getBounds());*/
  featureList = new List("features", {valueNames: ["feature-name"]});
  featureList.sort("feature-name", {order:"asc"});

<?php foreach ($layer as $l) : ?>
  var  <?php echo $l->slug; ?>BH = new Bloodhound({
    name: "<?php echo $l->layerName; ?>",
    datumTokenizer: function (d) {
      return Bloodhound.tokenizers.whitespace(d.name);
    },
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    local:  <?php echo $l->slug; ?>Search,
    limit: 10
  });

 <?php echo $l->slug; ?>BH.initialize();
<?php endforeach; ?>

  /* instantiate the typeahead UI */
  $("#searchbox").typeahead({
    minLength: 3,
    highlight: true,
    hint: false
<?php foreach ($layer as $l) : ?>
  }, {
    name: "<?php echo $l->layerName; ?>",
    displayKey: "name",
    source: <?php echo $l->slug; ?>BH.ttAdapter(),
    templates: {
      header: "<h4 class='typeahead-header'><img src='assets/img/<?php echo $l->markerImgName; ?>' width='24' height='28'>&nbsp;<?php echo $l->layerName; ?></h4>",
      suggestion: Handlebars.compile(["{{name}}<br>&nbsp;<small>{{address}}</small>"].join(""))
    }
<?php endforeach; ?>
  }).on("typeahead:selected", function (obj, datum) {
<?php foreach ($layer as $l) : ?>
    if (datum.source === "<?php echo $l->layerName; ?>") {
      if (!map.hasLayer(<?php echo $l->slug; ?>Layer)) {
        map.addLayer(<?php echo $l->slug; ?>Layer);
      }
      map.setView([datum.lat, datum.lng], 17);
      if (map._layers[datum.id]) {
        map._layers[datum.id].fire("click");
      }
    }
<?php endforeach; ?>
  }).on("typeahead:opened", function () {
    $(".navbar-collapse.in").css("max-height", $(document).height() - $(".navbar-header").height());
    $(".navbar-collapse.in").css("height", $(document).height() - $(".navbar-header").height());
  }).on("typeahead:closed", function () {
    $(".navbar-collapse.in").css("max-height", "");
    $(".navbar-collapse.in").css("height", "");
  });
  $(".twitter-typeahead").css("position", "static");
  $(".twitter-typeahead").css("display", "block");
});

// Leaflet patch to make layer control scrollable on touch browsers
var container = $(".leaflet-control-layers")[0];
if (!L.Browser.touch) {
  L.DomEvent
  .disableClickPropagation(container)
  .disableScrollPropagation(container);
} else {
  L.DomEvent.disableClickPropagation(container);
}
