  <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
<script src="https://npmcdn.com/csv2geojson@latest/csv2geojson.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

<style>
	   #map {
        height: 100%;
      }
</style>
        <div id="map">
        </div>
<script>
	final = "lat,lng\n";

      url = "https://stbus.azurewebsites.net/datahandler/taxi.php";
      $.getJSON(url, function (json) {
		lmao1 = json["value"];
		calltaxi();
	});

function calltaxi(){
for(i=0;i<lmao1.length;i++){
long = lmao1[i]["Longitude"];
lat = lmao1[i]["Latitude"];
comb = lat+","+long+"\n";
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


	function onEachFeature(feature, layer) {
		var popupContent = 'This is a taxi. What else were you expecting?';

		layer.bindPopup(popupContent);
	}


	taxilayer = L.geoJSON(finalfinal, {

		style: function (feature) {
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
	function onEachFeature(feature, layer) {
		var popupContent = 'This is a taxi. What else were you expecting?';

		layer.bindPopup(popupContent);
	}


	taxilayer = L.geoJSON(finalfinal, {

		style: function (feature) {
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

	taxilayer.addTo(wowihavetorenamethismap);
}

setInterval(function() {
taxilayer.remove();
      url = "https://stbus.azurewebsites.net/datahandler/taxi.php";
      $.getJSON(url, function (json) {
		lmao1 = json["value"];
		gengeojson();
	});
},90000)



</script>
