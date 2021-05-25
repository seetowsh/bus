<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Tow Bus v3</title>
    <style>
    #street-view {
      height: 600px;
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
    <link href="https://rawcdn.githack.com/Hi1307/ltabus/b896c394/flaticon.css" rel="stylesheet">
    <script defer src="https://code.getmdl.io/1.2.1/material.min.js"></script>
    <link rel="stylesheet" href="https://rawcdn.githack.com/Hi1307/epic-website/3ff373dba97086f43e3afa7c41bcdd0a206e0100/styles/index.css">
  </head>


  <body>
    <div id="sound"></div>

    <button disabled class="js-push-btn mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect">
      Enable Push Messaging
    </button>

    <section class="subscription-details js-subscription-details is-invisible">
      <pre><code class="js-subscription-json"></code></pre>
    </section>

    <div class="container">
      <div class="row">
        <h1 class="center"><span id="hm"></span></h1>
        <a class="btn waves-effect waves-light modal-trigger" href="#map1">Street view</a> <br>
        <table>
          <thead>
            <th style=min-width:90px>Bus Number</th>
            <th style=min-width:90px>Time</th>
            <th style=min-width:90px>Features</th>
            <th style=min-width:90px>Time (2)</th>
            <th style=min-width:90px>Features (2)</th>
            <th style=min-width:90px>Time (3)</th>
            <th style=min-width:90px>Features (3)</th>
          </thead>
        </table>
      </div>
    </div>

    <div id="map1" class="modal">
      <div class="modal-content">
      <div id="street-view">
      </div></div>
    <div class="modal-footer">
      <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
    </div>
  </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://rawcdn.githack.com/Hi1307/epic-website/fbc9b31fb17e548df6a03ca793361c6e2d1dc676/templates/busapp.js"></script>
    <script src="https://rawcdn.githack.com/Hi1307/epic-website/145e340a90c74b45467bb1a4706621f63afd3e30/templates/busapp2.js"></script>

    <script>
      let searchParams = new URLSearchParams(window.location.search);
      stopno = searchParams.get('stop')
      url = "https://arrivelah.herokuapp.com/?id="+searchParams.get('stop');
      $("#hm").append(bus_dict[parseInt(stopno)]);
      $.getJSON(url, function (json) {
        services = json["services"];
        for(i=0;i<services.length; i++) {
          let busno = services[i]["no"];
          let arrtimems = services[i]["next"]["duration_ms"];
          let arrtimemin = arrtimems/60000;
          let arrtime = Math.round( arrtimemin * 10 ) / 10;
          let arrtime2ms = services[i]["subsequent"]["duration_ms"];
          let arrtime2min = arrtime2ms/60000;
          let arrtime2 = Math.round( arrtime2min * 10 ) / 10;
          let arrtime3ms = services[i]["next3"]["duration_ms"];
          let arrtime3min = arrtime3ms/60000;
          let arrtime3 = Math.round( arrtime3min * 10 ) / 10;
          if(arrtime <= 0.5) {
            arrtime="Arriving";
          }
          else {
            arrtime=arrtime+" mins";
          }
          if(services[i]["next"]["load"] == "SDA") {
            load = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["next"]["load"] == "SEA") {
            load = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else {
            load = '<i class="material-icons">wc</i>';
          }
          if(services[i]["next"]["type"] == "SD") {
            deck = '<i class="flaticon-056-bus-3">';
          } else {
            deck = '<i class="flaticon-101-double-decker-bus">';
          }
          if(arrtime2 <= 0.5) {
            arrtime2="Arriving";
          } else {
            arrtime2 = arrtime2+" mins";
          }
          if(services[i]["subsequent"]["load"] == "SDA") {
            load2 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["subsequent"]["load"] == "SEA") {
            load2 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else {
            load2 = '<i class="material-icons">wc</i>';
          }
          if(services[i]["subsequent"]["type"] == "SD") {
            deck2 = '<i class="flaticon-056-bus-3">';
          } else {
            deck2 = '<i class="flaticon-101-double-decker-bus">';
          }
          if(arrtime3 <= 0.5) {
            arrtime3="Arriving";
          } else {
            arrtime3 = arrtime3+" mins";
          }
          if(services[i]["next3"]["load"] == "SDA") {
            load3 = '<i class="material-icons">directions_walk</i>';
          } else if(services[i]["next3"]["load"] == "SEA"){
            load3 = '<i class="material-icons">airline_seat_recline_normal</i>';
          } else {
            load3 = '<i class="material-icons">wc</i>';
          }
          if(services[i]["next3"]["type"] == "SD"){
            deck3 = '<i class="flaticon-056-bus-3">';
          } else {
            deck3 = '<i class="flaticon-101-double-decker-bus">';
          }
          $("table").append('<tr id="' + busno + '"><td>' +busno+'</td><td><a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["next"]["lat"]+','+services[i]["next"]["lng"]+'">'+arrtime+'</a></td><td><i class="material-icons">accessible</i>'+load+deck+'</td><td><a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["subsequent"]["lat"]+','+services[i]["subsequent"]["lng"]+'">'+arrtime2+'</a></td><td><i class="material-icons">accessible</i>'+load2+deck2+'</td><td><a target="_blank" href="https://www.google.com.sg/maps/search/'+services[i]["next3"]["lat"]+','+services[i]["next3"]["lng"]+'">'+arrtime3+'</a></td><td><i class="material-icons">accessible</i>'+load3+deck3+'</td></tr>');
          $('#' + busno)[0].addEventListener('click', function() {
            const title = busno;
            const options = {
              body: 'Simple piece of body text.\nSecond line of body text :)'
            };
            let delay = arrtimems-120000
            setTimeout(function() {swRegistration.showNotification(title, options);
                                   playSound("musics/asifitsyourlast")}, delay)
          })
        }
        function playSound(filename){
        var mp3Source = '<source src="' + filename + '.mp3" type="audio/mpeg">';
        /*var oggSource = '<source src="' + filename + '.ogg" type="audio/ogg">';*/
        var embedSource = '<embed hidden="true" autostart="true" loop="false" src="' + filename +'.mp3">';
        document.getElementById("sound").innerHTML='<audio autoplay="autoplay">' + mp3Source /*+ oggSource*/ + embedSource + '</audio>';
      }
      });
    </script>
    <script>
    var panorama;
    function initialize() {
      panorama = new google.maps.StreetViewPanorama(
          document.getElementById('street-view'),
          {
            position: {lat: bus_dict2[parseInt(stopno)][0], lng: bus_dict2[parseInt(stopno)][1]},
            pov: {heading: 0, pitch: 0},
            zoom: 1
          });
    }
    $(document).ready(function(){
      $('.modal').modal();
    });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <script async defer
         src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC5jUfWC1Us-pdmcOUCKUzp1HWHMJN8OSU&callback=initialize">
    </script>
    <script>
      $('.modal').modal();
    </script>

    <script src="/scripts/main.js"></script>
    <script src="https://code.getmdl.io/1.2.1/material.min.js"></script>
  </body>
</html>