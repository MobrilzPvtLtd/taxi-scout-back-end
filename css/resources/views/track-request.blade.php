<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request - Tagxi</title>
    <link rel="shortcut icon" href="{{ fav_icon() ?? asset('assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{!! asset('css/track-request.css') !!}">
</head>

<style>
    #map {
        height: 400px;
        width: 100%;
        padding: 10px;
    }

    th {
        text-align: center;
    }

    td {
        text-align: center;
    }

    .highlight {
        color: red;
        font-weight: 800;
        font-size: large;
    }

/*timeline*/
@media (min-width:992px) {
    .page-container {
        max-width: 1140px;
        margin: 0 auto
    }

    .page-sidenav {
        display: block !important
    }
}

.padding {
    padding: 2rem
}

.w-32 {
    width: 32px !important;
    height: 32px !important;
    font-size: .85em
}

.tl-item .avatar {
    z-index: 2
}

.circle {
    border-radius: 500px
}

.gd-warning {
    color: #fff;
    border: none;
    background: #f4c414 linear-gradient(45deg, #f4c414, #f45414)
}

.timeline {
    position: relative;
    border-color: rgba(160, 175, 185, .15);
    padding: 0;
    margin: 0
}

.p-4 {
    padding: 1.5rem !important
}

.block,
.card {
    background: #fff;
    border-width: 0;
    border-radius: .25rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .05);
    margin-bottom: 1.5rem
}

.mb-4,
.my-4 {
    margin-bottom: 1.5rem !important
}

.tl-item {
    border-radius: 3px;
    position: relative;
    display: -ms-flexbox;
    display: flex
}

.tl-item>* {
    padding: 10px
}

.tl-item .avatar {
    z-index: 2
}

.tl-item:last-child .tl-dot:after {
    display: none
}

.tl-item.active .tl-dot:before {
    border-color: #448bff;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-item:last-child .tl-dot:after {
    display: none
}

.tl-item.active .tl-dot:before {
    border-color: #34b807;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-dot {
    position: relative;
    border-color: rgba(160, 175, 185, .15)
}

.tl-dot:after,
.tl-dot:before {
    content: '';
    position: absolute;
    border-color: inherit;
    border-width: 2px;
    border-style: solid;
    border-radius: 50%;
    width: 10px;
    height: 10px;
    top: 15px;
    left: 50%;
    transform: translateX(-50%)
}

.tl-dot:after {
    width: 0;
    height: auto;
    top: 25px;
    bottom: -15px;
    border-right-width: 0;
    border-top-width: 0;
    border-bottom-width: 0;
    border-radius: 0
}

tl-item.active .tl-dot:before {
    border-color:#34b807;
    box-shadow: 0 0 0 4px rgba(68, 139, 255, .2)
}

.tl-dot {
    position: relative;
    border-color: rgba(160, 175, 185, .15)
}

.tl-dot:after,
.tl-dot:before {
    content: '';
    position: absolute;
    border-color: inherit;
    border-width: 2px;
    border-style: solid;
    border-radius: 50%;
    width: 10px;
    height: 10px;
    top: 15px;
    left: 50%;
    transform: translateX(-50%)
}

.tl-dot:after {
    width: 0;
    height: auto;
    top: 25px;
    bottom: -15px;
    border-right-width: 0;
    border-top-width: 0;
    border-bottom-width: 0;
    border-radius: 0
}

.tl-content p:last-child {
    margin-bottom: 0
}

.tl-date {
    font-size: .85em;
    margin-top: 2px;
    min-width: 100px;
    max-width: 100px
}

.avatar {
    position: relative;
    line-height: 1;
    border-radius: 500px;
    white-space: nowrap;
    font-weight: 700;
    border-radius: 100%;
    display: -ms-flexbox;
    display: flex;
    -ms-flex-pack: center;
    justify-content: center;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-negative: 0;
    flex-shrink: 0;
    border-radius: 500px;
    box-shadow: 0 5px 10px 0 rgba(50, 50, 50, .15)
}

.b-warning {
    border-color: #b1b1b1!important;
}

.b-primary {
    border-color: #f63f3f!important;
}

.b-danger {
    border-color: #f54394!important;
}
/*timeline end*/
</style>

<body class="bg-white-400">

    @if($request->is_completed || $request->is_cancelled)
    <div id="completed-design" class="flex justify-center h-screen">
        <div class="flex-column text-black font-bold rounded-lg mt-40">
            <div class="flex justify-center">
                <img src="{{ asset('map/tick.png') }}" alt="" class="rounded w-10 h-10">
            </div>
            <p class="mt-5">@lang('view_pages.the_trip_has_ended')</p>
        </div>
    </div>
    @else
    <div class="lg:flex">
        <div class="lg:w-1/2 sm:w-full md:w-full sm:h-screen md:h-screen">
            <!-- Trip Details bg-orange-300 shadow-lg-->
            <div class="m-1 p-2 rounded shadow-lg">
                <div class="mx-auto flex justify-center items-center mb-5">
                    <!-- <div class="w-full text-center"> -->
                    <strong class="text-blue-900">{{ $request->request_number }} - </strong>
                    <p class="text-md text-black font-bold ml-3 trip_status"></p>


                    <!-- </div> -->
                </div>
                <hr>

      <!-- Map -->
            <div class="lg:mt-10 mt-6">
                <div id="map"></div>
            </div>

                    <p class="text-md text-black font-bold ml-3 ride_otp"></p>
                
            </div>



            <!-- Driver Details -->
            <div class="bg-white rounded shadow-lg m-5 p-3 lg:mt-10">
                <div class="flex justify-between">

                    <div class="flex items-center">
                        <img src="{{ $request->driverDetail->user->profile_pic ?? 'https://cdn4.iconfinder.com/data/icons/rcons-user/32/child_boy-128.png' }}" alt="" class="rounded-full h-12 w-12 flex items-center justify-center" width="50" height="50">

                        <div class="flex-column ml-2">
                            <p class="text-gray-900">{{ ucfirst($request->driverDetail->name) }}</p>

                            <p class="flex flex-row">
                                @for($i = 0; $i < $request->driverDetail->user->rating; $i++)
                                    <img src="https://cdn2.iconfinder.com/data/icons/ios-7-icons/50/star-128.png" alt="" class="h-4 w-4 items-center justify-center bg-yellow">
                                @endfor
                            </p>

                        </div>
                        <div class="flex items-center ml-3 text-center">
                        <p style="color:red;">
                            @lang('view_pages.estimated_price')
                        {{$request->requested_currency_symbol}}  {{ $request->request_eta_amount}}
                        </p>
                    </div>

                    </div>

                    
                    <div class="flex items-center">
                    <ul class="box-controls pull-right">
                    <li>
                    <div class="flex" style="border:1px solid black;padding: 5px;margin-left: 5px;">
                        <p>{{ $request->driverDetail->car_number }}</p>
                    </div>
                    </li>
                     <li>
                        <p class="ml-2 text-gray-900">{{ $request->driverDetail->carMake->name }}</p>
                        <p class="ml-2 text-gray-900">{{ $request->driverDetail->carModel->name }}</p>
                    </li>
                        </div>
                    </ul>

                    </div>
                </div>

                <hr>

                <div class="flex justify-between m-2" style="margin-left: 35%;padding: 20px;">
                    <div class="flex"  >
                        <p >
                            <a href="tel:{{ $request->driverDetail->mobile }}" style="display: flex;align-items: center;
                            ">
                                <img  src="{{asset('assets/img/Phone.svg')}}" style="width:30px">
                               @lang('view_pages.call_driver')
                          </a>
                        </p> 
                    </div>
                </div>

            </div>
  <!-- pickup & drop address -->

  <div class="page-content page-container" id="page-content">
    <div class="padding">
        <div class="row">
            
            <div class="col-lg-6">
                <p>Location Details</p>
                <div class="timeline p-4 block mb-4">
                    <div class="tl-item active">
                        <div class="tl-dot b-warning"></div>
                        <div class="tl-content">
                            <div class="">Pickup</div>
                            <div class="tl-date text-muted mt-1">{{ str_limit($request->requestPlace->pick_address,30) }}</div>
                        </div>
                    </div>
                    <div class="tl-item">
                        <div class="tl-dot b-primary"></div>
                        <div class="tl-content">
                            <div class="">Drop</div>
                            <div class="tl-date text-muted mt-1">{{ str_limit($request->requestPlace->drop_address,30) }}</div>
                        </div>
                    </div>

                </div>
            </div>
            
        
        </div>
    </div>
</div>

      <!-- pickup & drop address end-->
        </div>
    </div>




    <script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{get_settings('google_map_key')}}&sensor=false&libraries=places"></script>

    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-database.js"></script>
    <!-- TODO: Add SDKs for Firebase products that you want to use https://firebase.google.com/docs/web/setup#available-libraries -->
    <script src="https://www.gstatic.com/firebasejs/7.19.0/firebase-analytics.js"></script>

    <script type="text/javascript">
        var carimage = "{{ url('map/car.png') }}";
        var driverId = '{{ $request->driverDetail->id }}';
        var requestId = '{{ $request->id }}';
        var driverLat, driverLng, bearing;

        // Your web app's Firebase configuration
    var firebaseConfig = {
    apiKey: "{{get_settings('firebase-api-key')}}",
    authDomain: "{{get_settings('firebase-auth-domain')}}",
    databaseURL: "{{get_settings('firebase-db-url')}}",
    projectId: "{{get_settings('firebase-project-id')}}",
    storageBucket: "{{get_settings('firebase-storage-bucket')}}",
    messagingSenderId: "{{get_settings('firebase-messaging-sender-id')}}",
    appId: "{{get_settings('firebase-app-id')}}",
    measurementId: "{{get_settings('firebase-measurement-id')}}"
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);
        firebase.analytics();

var tripStatusElement = document.createElement('p'); // Create a new <p> element
tripStatusElement.classList.add('text-md', 'text-black', 'font-bold', 'ml-3', 'trip_status'); // Add classes to the element

var rideOtpElement = document.createElement('p'); // Create a new <p> element for ride OTP
rideOtpElement.classList.add('text-md', 'text-black', 'font-bold', 'ml-3', 'ride_otp'); // Add classes to the element

var tripRef = firebase.database().ref('requests/' + requestId);

tripRef.on('value', function(snapshot) {
    var data = snapshot.val();
    console.log(data);

    var tripStatusText = '';

    if (data.is_completed == true) {
        tripStatusText = '@lang("view_pages.trip_completed")';
        location.reload();
    } else if (data.is_cancelled == true) {
        tripStatusText = '@lang("view_pages.trip_cancelled")';
    } else if (data.trip_start == "1") {
        tripStatusText = '@lang("view_pages.trip_started")';

    } else if (data.trip_arrived == "1") {
        tripStatusText = '@lang("view_pages.driver_arrived")';

        // Display ride OTP and set its value
        rideOtpElement.textContent = '@lang("view_pages.ride_otp") : ' + '{{ $request->ride_otp }}';

        // Replace the existing trip status element
        var existingTripStatusElement = document.querySelector('.trip_status');
        if (existingTripStatusElement) {
            existingTripStatusElement.replaceWith(tripStatusElement);
        }
        
        // Append the ride OTP element
        var rideOtpContainer = document.querySelector('.ride_otp');
        if (rideOtpContainer) {
            rideOtpContainer.replaceWith(rideOtpElement);
        }
    } else {
        tripStatusText = '@lang("view_pages.driver_is_on_the_way")';

    }

        tripStatusElement.textContent = tripStatusText;

        var existingTripStatusElement = document.querySelector('.trip_status'); 
        if (existingTripStatusElement) {
            existingTripStatusElement.replaceWith(tripStatusElement); 
        }
    });


        var tripRef = firebase.database().ref('requests/' + requestId);

        // tripRef.on('value', async function(snapshot) {

        //     var data = snapshot.val();

        //     console.log(data);

        //     driverLat = data.lat;
        //     driverLng = data.lng;
        //     bearing = data.bearing;

        //     await loadCarInMap(driverLat, driverLng, bearing, carimage);

        //     // await rotateMarker(bearing);
        // });


        var area1, area2, icon1, icon2;

        area1 = "{{ $request->pick_address }}";
        area2 = "{{ $request->drop_address }}";
        icon1 = "{{ url('map/start_pin_flag.png') }}";
        icon2 = "{{ url('map/end_pin_flag.png') }}";

        var locations = [
            [area1, "{{ $request->pick_lat }}", "{{ $request->pick_lng }}", icon1],
            [area2, "{{ $request->drop_lat == null ? $request->pick_lat : $request->drop_lat }}", "{{ $request->drop_lng == null ? $request->pick_lng : $request->drop_lng }}", icon2],
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: new google.maps.LatLng(locations[1][1], locations[1][2]),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // map new
        var infowindow = new google.maps.InfoWindow();
        var marker, i, carIcon;

        var markers = new Array();
        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                icon: locations[i][3],
                map: map
            });
            markers.push(marker);
            marker.setMap(map);

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));
        }

        function loadCarInMap(driverLat, driverLng, bearing, carimage) {
            var icon = {
                url: carimage
            };

            icon.rotation += bearing;

            carIcon = new google.maps.Marker({
                title: 'carIcon',
                icon: icon,
                position: new google.maps.LatLng(driverLat, driverLng)
            });

            deleteCarIcon(carIcon);

            markers.push(carIcon);
            carIcon.setMap(map);

            setTimeout(() => {
                rotateMarker(carimage, bearing);
            }, 3000);
        }


        function rotateMarker(carimage, bearing) {
            document.querySelector(`img[src='${carimage}']`).style.transform = 'rotate(' + bearing + 'deg)';
            // document.querySelector("img[src='http://localhost/future/public/map/car.png']").style.transform = 'rotate(80deg)'
        }

        function deleteCarIcon() {
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].hasOwnProperty('title')) {
                    if (markers[i].title == 'carIcon') {
                        markers[i].setMap(null);
                    }
                }
            }
        }
    </script>

    @endif

</body>

</html>