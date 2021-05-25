<html lang="en" dir="ltr">
  <head>
  <!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-52921261-6"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'UA-52921261-6');
</script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - See Tow Bus</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet">
      <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script src="https://npmcdn.com/csv2geojson@latest/csv2geojson.js"></script>
    <style>
    #street-view {
      height: 600px;
    }
    #map {
        height: 500px;
      }
    @media (prefers-color-scheme: dark) {
      body{
        background-color: #333;
        color: white !important;
      }
      button, input, optgroup, select, textarea {
        color: white !important;
      }
      .input-field label {
        color: white;
      }
    }
    body {
      font-family: "Helvetica Neue", Helvetica, Arial !important;
    }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://rawcdn.githack.com/Hi1307/epic-website/3ff373dba97086f43e3afa7c41bcdd0a206e0100/styles/index.css">
  </head>
  <body>
    <div id="sound"></div>
    <div class="container">
      <div class="row center">
        <h1 class="center"><span id="hm"></span></h1>
        <p class="center"><a class="btn waves-effect waves-light" href="/"><i class="material-icons">arrow_back</i></a><a class="btn waves-effect waves-light modal-trigger" href="#map1">Street view</a><a class="btn waves-effect waves-light" onClick="updatecookie();"><i class="material-icons">star</i></a></p>
        <br>
        <table class="striped responsive-table">
          <thead>
            <th>Legend</th>
          </thead>
          <tr>
            <td>Space</td><td><i class="material-icons">airline_seat_recline_normal</i>Seating available</td><td><i class="material-icons">directions_walk</i>Standing space</td><td><i class="material-icons">wc</i>Limited standing space</td>
          </tr>
          <tr>
            <td>Wheelchair Access</td><td><i style="color:red" class="material-icons">accessible</i>No Wheelchairs</td>
          </tr>
          <tr>
            <td>Type of bus</td><td><i class="material-icons">filter_1</i> Single decker</td><td><i class="material-icons">filter_2</i> Double decker</td><td>&#127345;&#65039; Bendy/Articulated bus</td>
          </tr>
        </table>
        <table>
          <thead>
            <th style=min-width:90px>Bus Number</th>
            <th style=min-width:90px>Time (1)</th>
            <th style=min-width:90px>Time (2)</th>
            <th style=min-width:90px>Time (3)</th>
          </thead><tbody id="time">
          <tr id="fetching"><td colspan="5"><p style="text-align:center">Calculating timings. Please wait.</p></td></tr>
        </tbody></table>
      </div>

      <div id="map"></div>
    </div>
    <div id="map1" class="modal">
      <div class="modal-content">
        <div id="street-view">
        </div>
      </div>
      <div class="modal-footer">
        <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
      </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://rawcdn.githack.com/Hi1307/epic-website/fbc9b31fb17e548df6a03ca793361c6e2d1dc676/templates/busapp.js"></script>
    <script src="https://rawcdn.githack.com/Hi1307/epic-website/145e340a90c74b45467bb1a4706621f63afd3e30/templates/busapp2.js"></script>
    <script>

      function minTommss(minutes){
 var sign = minutes < 0 ? "-" : "";
 var min = Math.floor(Math.abs(minutes));
 var sec = Math.floor((Math.abs(minutes) * 60) % 60);
 return sign + (min < 10 ? "0" : "") + min + ":" + (sec < 10 ? "0" : "") + sec;
};


      let searchParams = new URLSearchParams(window.location.search);
      stopno = searchParams.get('stop');
      url = "https://stbus.azurewebsites.net/datahandler/arrival.php?id="+searchParams.get('stop');
      $("#hm").append(bus_dict[parseInt(stopno)]+' ('+stopno+')');
      $.getJSON(url, function (json) {
        services = json["Services"];
        callbus();


        $("#fetching").remove();
        let timenow = Date.now();

          if (services.length == 0) {
          $("#time").append('<tr><td colspan="5"><p style="text-align:center">Looks like we didn\'t get any buses. This could be because of 1 of several reasons:</p> <ul><li><p>LTA is down for reasons beyond our control</p></li><p>It\'s late at night</p><p>You have no internet connection</p></ul><p>The bus stop ID is invalid.</p></ul>  </td></tr>');
          } else {
        for(i=0;i<services.length; i++) {
          let busno = services[i]["ServiceNo"];
          let arrtimemsfake = services[i]["NextBus"]["EstimatedArrival"];
          let arrtimefake = +new Date(arrtimemsfake);
          let arrtimems = arrtimefake - timenow;
          let arrtimemin = arrtimems/60000;
          let arrtime = minTommss(arrtimemin);
          let arrtime2msfake = services[i]["NextBus2"]["EstimatedArrival"];
          let arrtime2fake = +new Date(arrtime2msfake);
          let arrtime2ms = arrtime2fake - timenow;
          let arrtime2min = arrtime2ms/60000;
          let arrtime2 = minTommss(arrtime2min)
          let arrtime3msfake = services[i]["NextBus3"]["EstimatedArrival"];
          let arrtime3fake = +new Date(arrtime3msfake);
          let arrtime3ms = arrtime3fake - timenow;
          let arrtime3min = arrtime3ms/60000;
          let arrtime3 = minTommss(arrtime3min)
          if(arrtime.charAt(0) == "-") {
            arrtime="Arriving";
          }
          else {
            arrtime=arrtime+"&nbsp;mins";
          }
          if(services[i]["NextBus"]["Feature"] == "WAB" || services[i]["NextBus"]["Load"] == ""){
            wab = ''
          } else {
            wab = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus"]["Load"] == "SDA") {
            load = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus"]["Load"] == "SEA") {
            load = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus"]["Load"] == "LSD"){
            load = '<i class="material-icons">wc</i>';
          }
          if(services[i]["NextBus"]["Type"] == "SD") {
            deck = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus"]["Type"] == "DD") {
            deck = '<i class="material-icons">filter_2</i> ';
          } else {
            deck = '&#127345;&#65039;';
          }
          if(arrtime2 == "NaN:NaN") {
            arrtime2="";
          } else {
            arrtime2 = arrtime2+"&nbsp;mins";
          }
          if(services[i]["NextBus2"]["Feature"] == "WAB" || services[i]["NextBus2"]["Load"] == ""){
            wab2 = ''
          } else {
            wab2 = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus2"]["Load"] == "SDA") {
            load2 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus2"]["Load"] == "SEA") {
            load2 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus2"]["Load"] == "LSD"){
            load2 = '<i class="material-icons">wc</i>';
          } else {
            load2 = ""
          }
          if(services[i]["NextBus2"]["Type"] == "SD") {
            deck2 = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus2"]["Type"] == "DD") {
            deck2 = '<i class="material-icons">filter_2</i> ';
          } else if(services[i]["NextBus2"]["Type"] == "BD"){
            deck2 = '&#127345;&#65039;';
          } else {
            deck2 = ""
          }
          if(arrtime3 == "NaN:NaN") {
            arrtime3="";
          } else {
            arrtime3 = arrtime3+"&nbsp;mins";
          }
          if(services[i]["NextBus3"]["Feature"] == "WAB" || services[i]["NextBus3"]["Load"] == ""){
            wab3 = ''
          } else {
            wab3 = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus3"]["Load"] == "SDA") {
            load3 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus3"]["Load"] == "SEA"){
            load3 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus3"]["Load"] == "LSD"){
            load3 = '<i class="material-icons">wc</i>';
          } else{
            load3 = ""
          }
          if(services[i]["NextBus3"]["Type"] == "SD") {
            deck3 = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus3"]["Type"] == "DD") {
            deck3 = '<i class="material-icons">filter_2</i> ';
          } else if(services[i]["NextBus3"]["Type"] == "BD"){
            deck3 = '&#127345;&#65039;';
          } else {
            deck3 = ""
          }
          $("#time").append('<tr><td id="bus'+i+'">' +busno+'</td><td>'+wab+load+deck+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus"]["Latitude"]+','+services[i]["NextBus"]["Longitude"]+'">'+arrtime+'</a></td><td>'+wab2+load2+deck2+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus2"]["Latitude"]+','+services[i]["NextBus2"]["Longitude"]+'">'+arrtime2+'</a></td><td>'+wab3+load3+deck3+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus3"]["Latitude"]+','+services[i]["NextBus3"]["Longitude"]+'">'+arrtime3+'</a></td></tr>');

        }}

        if(stopno == "66271" && services.length !== 0) {
          jQuery("#bus0").append("<br>To Ang Mo Kio");
          jQuery("#bus1").append("<br>To Punggol");
        } else if(stopno == "46088" && services.length !== 0){
          jQuery("#bus0").append("<br>To Kranji");
          jQuery("#bus1").append("<br>To Malaysia");
        } else if(stopno == "46069" && services.length !== 0){
          jQuery("#bus0").append("<br>To Kranji");
          jQuery("#bus1").append("<br>To Malaysia");
        }
      });
    </script>
    <script>
setInterval(function(){
          $("#time").empty();
        let timenow = Date.now();

          if (services.length == 0) {
          $("#time").append('<tr><td colspan="5"><p style="text-align:center">Looks like we didn\'t get any buses. This could be because of 1 of several reasons:</p> <ul><li><p>LTA is down for reasons beyond our control</p></li><p>It\'s late at night</p><p>You have no internet connection</p></ul><p>The bus stop ID is invalid.</p></ul>  </td></tr>');
          } else {
        for(i=0;i<services.length; i++) {
          let busno = services[i]["ServiceNo"];
          let arrtimemsfake = services[i]["NextBus"]["EstimatedArrival"];
          let arrtimefake = +new Date(arrtimemsfake);
          let arrtimems = arrtimefake - timenow;
          let arrtimemin = arrtimems/60000;
          let arrtime = minTommss(arrtimemin);
          let arrtime2msfake = services[i]["NextBus2"]["EstimatedArrival"];
          let arrtime2fake = +new Date(arrtime2msfake);
          let arrtime2ms = arrtime2fake - timenow;
          let arrtime2min = arrtime2ms/60000;
          let arrtime2 = minTommss(arrtime2min)
          let arrtime3msfake = services[i]["NextBus3"]["EstimatedArrival"];
          let arrtime3fake = +new Date(arrtime3msfake);
          let arrtime3ms = arrtime3fake - timenow;
          let arrtime3min = arrtime3ms/60000;
          let arrtime3 = minTommss(arrtime3min)
          if(arrtime.charAt(0) == "-") {
            arrtime="Arriving";
          }
          else {
            arrtime=arrtime+"&nbsp;mins";
          }
          if(services[i]["NextBus"]["Feature"] == "WAB" || services[i]["NextBus"]["Load"] == ""){
            wab = ''
          } else {
            wab = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus"]["Load"] == "SDA") {
            load = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus"]["Load"] == "SEA") {
            load = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus"]["Load"] == "LSD"){
            load = '<i class="material-icons">wc</i>';
          }
          if(services[i]["NextBus"]["Type"] == "SD") {
            deck = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus"]["Type"] == "DD") {
            deck = '<i class="material-icons">filter_2</i> ';
          } else {
            deck = '&#127345;&#65039;';
          }
          if(arrtime2 == "NaN:NaN") {
            arrtime2="";
          } else {
            arrtime2 = arrtime2+"&nbsp;mins";
          }
          if(services[i]["NextBus2"]["Feature"] == "WAB" || services[i]["NextBus2"]["Load"] == ""){
            wab2 = ''
          } else {
            wab2 = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus2"]["Load"] == "SDA") {
            load2 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus2"]["Load"] == "SEA") {
            load2 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus2"]["Load"] == "LSD"){
            load2 = '<i class="material-icons">wc</i>';
          } else {
            load2 = ""
          }
          if(services[i]["NextBus2"]["Type"] == "SD") {
            deck2 = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus2"]["Type"] == "DD") {
            deck2 = '<i class="material-icons">filter_2</i> ';
          } else if(services[i]["NextBus2"]["Type"] == "BD"){
            deck2 = '&#127345;&#65039;';
          } else {
            deck2 = ""
          }
          if(arrtime3 == "NaN:NaN") {
            arrtime3="";
          } else {
            arrtime3 = arrtime3+"&nbsp;mins";
          }
          if(services[i]["NextBus3"]["Feature"] == "WAB" || services[i]["NextBus3"]["Load"] == ""){
            wab3 = ''
          } else {
            wab3 = '<i style="color:red" class="material-icons">accessible</i>'
          }
          if(services[i]["NextBus3"]["Load"] == "SDA") {
            load3 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["NextBus3"]["Load"] == "SEA"){
            load3 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else if(services[i]["NextBus3"]["Load"] == "LSD"){
            load3 = '<i class="material-icons">wc</i>';
          } else{
            load3 = ""
          }
          if(services[i]["NextBus3"]["Type"] == "SD") {
            deck3 = '<i class="material-icons">filter_1</i> ';
          } else if(services[i]["NextBus3"]["Type"] == "DD") {
            deck3 = '<i class="material-icons">filter_2</i> ';
          } else if(services[i]["NextBus3"]["Type"] == "BD"){
            deck3 = '&#127345;&#65039;';
          } else {
            deck3 = ""
          }
          $("#time").append('<tr><td id="bus'+i+'">' +busno+'</td><td>'+wab+load+deck+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus"]["Latitude"]+','+services[i]["NextBus"]["Longitude"]+'">'+arrtime+'</a></td><td>'+wab2+load2+deck2+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus2"]["Latitude"]+','+services[i]["NextBus2"]["Longitude"]+'">'+arrtime2+'</a></td><td>'+wab3+load3+deck3+'<a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["NextBus3"]["Latitude"]+','+services[i]["NextBus3"]["Longitude"]+'">'+arrtime3+'</a></td></tr>');

        }}

        if(stopno == "66271" && services.length !== 0) {
          jQuery("#bus0").append("<br>To Ang Mo Kio");
          jQuery("#bus1").append("<br>To Punggol");
        } else if(stopno == "46088" && services.length !== 0){
          jQuery("#bus0").append("<br>To Kranji");
          jQuery("#bus1").append("<br>To Malaysia");
        } else if(stopno == "46069" && services.length !== 0){
          jQuery("#bus0").append("<br>To Kranji");
          jQuery("#bus1").append("<br>To Malaysia");
        }
}, 1000);


final = "busno,busq,lat,lng\n"

function callbus(){
for(i=0;i<services.length;i++){
busno = services[i]["ServiceNo"];
lat1 = services[i]["NextBus"]["Latitude"];
long1 = services[i]["NextBus"]["Longitude"];
comb = busno+",First bus,"+lat1+","+long1+"\n";
lat2 = services[i]["NextBus2"]["Latitude"];
long2 = services[i]["NextBus2"]["Longitude"];
lat3 = services[i]["NextBus3"]["Latitude"];
long3 = services[i]["NextBus3"]["Longitude"];

if(typeof lat2 !== "undefined") {
comb = comb+busno+",Second bus,"+lat2+","+long2+"\n";
};
if(typeof lat3 !== "undefined") {
comb = comb+busno+",Third bus,"+lat3+","+long3+"\n";
};
final = final+comb
  }

  csv2geojson.csv2geojson(final, {
    latfield: 'lat',
    lonfield: 'lng',
    delimiter: ','
}, function(err, data) {
  finalfinal = data
});

        wowihavetorenamethismap = new L.Map('map', {
          center: {lat: 1.290270, lng: 103.851959},
          zoom: 17,
          minZoom: 14,
          layers: new L.TileLayer("https://maps-{s}.onemap.sg/v3/Default/{z}/{x}/{y}.png",{
  bounds: [[1.506417, 103.552020], [1.199572, 104.058765]],
  attribution: 'Map data &copy; 2019 <a href="http://SLA.gov.sg">Singapore Land Authority</a>'
}),
        });
         wowihavetorenamethismap.locate({setView: true, maxZoom: 17});
         busstoploc = L.marker([bus_dict2[parseInt(stopno)][0], bus_dict2[parseInt(stopno)][1]]).addTo(wowihavetorenamethismap);


  function onEachFeature(feature, layer) {
    var popupContent = feature.properties.busno+' - '+feature.properties.busq+'</p>';

    layer.bindPopup(popupContent);
  }


  buslayer = L.geoJSON(finalfinal, {

    style: function (feature) {
      return feature.properties && feature.properties.style;
    },

    onEachFeature: onEachFeature,

        pointToLayer: function (feature, latlng) {
      return L.circleMarker(latlng, {
        radius: 6,
        fillColor: "#ff7800",
        color: "#000",
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
      });
    }

  }).addTo(wowihavetorenamethismap);

}

function gengeojson(){

  for(i=0;i<services.length;i++){
busno = services[i]["ServiceNo"];
lat1 = services[i]["NextBus"]["Latitude"];
long1 = services[i]["NextBus"]["Longitude"];
comb = busno+",First bus,"+lat1+","+long1+"\n";
lat2 = services[i]["NextBus2"]["Latitude"];
long2 = services[i]["NextBus2"]["Longitude"];
lat3 = services[i]["NextBus3"]["Latitude"];
long3 = services[i]["NextBus3"]["Longitude"];

if(typeof lat2 !== "undefined") {
comb = comb+busno+",Second bus,"+lat2+","+long2+"\n";
};
if(typeof lat3 !== "undefined") {
comb = comb+busno+",Third bus,"+lat3+","+long3+"\n";
};
final = final+comb
  }

  csv2geojson.csv2geojson(final, {
    latfield: 'lat',
    lonfield: 'lng',
    delimiter: ','
}, function(err, data) {
  finalfinal = data
});



  function onEachFeature(feature, layer) {
    var popupContent = feature.properties.busno+' - '+feature.properties.busq+'</p>';

    layer.bindPopup(popupContent);
  }


  buslayer = L.geoJSON(finalfinal, {

    style: function (feature) {
      return feature.properties && feature.properties.style;
    },
    
    onEachFeature: onEachFeature,

        pointToLayer: function (feature, latlng) {
      return L.circleMarker(latlng, {
        radius: 6,
        fillColor: "#ff7800",
        color: "#000",
        weight: 1,
        opacity: 1,
        fillOpacity: 0.8
      });
    }
  })

  buslayer.addTo(wowihavetorenamethismap);
}

</script>
    <script>
      var panorama;
      function initialize() {
        panorama = new google.maps.StreetViewPanorama(document.getElementById('street-view'),
          {
            position: {lat: bus_dict2[parseInt(stopno)][0], lng: bus_dict2[parseInt(stopno)][1]},
            pov: {heading: 0, pitch: 0},
            zoom: 1
          });
      }
      $(document).ready(function(){
        $('.modal').modal();
      });
function getCookie(name) {
  var re = new RegExp(name + "=([^;]+)");
  var value = re.exec(document.cookie);
  return (value != null) ? unescape(value[1]) : null;
}
favourite = getCookie("fav")
    function updatecookie() {
      document.cookie = "fav="+favourite+","+stopno+";path=/";
      alert("The bus stop "+bus_dict[parseInt(stopno)]+" has been added!")
    }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?callback=initialize"></script>
    <script>
setInterval(function(){
buslayer.remove();

      $.getJSON(url, function (json) {
        services = json["Services"];
        gengeojson()
        });
          }, 30000)
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
  </body>
</html>