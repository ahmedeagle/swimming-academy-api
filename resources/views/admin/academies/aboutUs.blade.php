@extends('admin.layouts.basic')
@section('title')
    بيانات التواصل / من نحن
@stop
@section('style')

@stop
@section('content')
    <div class="app-content content">
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-6 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">الرئيسية </a>
                                </li>
                                <li class="breadcrumb-item active">بيانات الاكاديمية
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Basic form layout section start -->
                <section id="basic-form-layouts">
                    <div class="row match-height">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title" id="basic-layout-form">من نحن / بيانات التواصل </h4>
                                    <a class="heading-elements-toggle"><i
                                            class="la la-ellipsis-v font-medium-3"></i></a>
                                    <div class="heading-elements">
                                        <ul class="list-inline mb-0">
                                            <li><a data-action="collapse"><i class="ft-minus"></i></a></li>
                                            <li><a data-action="reload"><i class="ft-rotate-cw"></i></a></li>
                                            <li><a data-action="expand"><i class="ft-maximize"></i></a></li>
                                            <li><a data-action="close"><i class="ft-x"></i></a></li>
                                        </ul>
                                    </div>
                                </div>
                                @include('admin.includes.alerts.success')
                                @include('admin.includes.alerts.errors')
                                <div class="card-content collapse show">
                                    <div class="card-body">
                                        <form class="form" action="{{route('admin.academies.postaboutus')}}"
                                              method="POST"
                                              enctype="multipart/form-data">
                                            @csrf

                                            <input type="hidden" name="academy_id" value="{{$academy -> id }}">
                                            <div class="form-body">
                                                <h4 class="form-section"><i class="ft-user"></i> بيانات التواصل </h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> البريد الالكتروني
                                                                للاكاديمية </label>
                                                            <input type="text" value="{{ @$settings -> email }}"
                                                                   id="email"
                                                                   class="form-control"
                                                                   placeholder=" بريد الالكتروني للاكاديمية  "
                                                                   name="email">
                                                            @error('email')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> رقم التواصل مع
                                                                الاكاديمية </label>
                                                            <input type="text" value="{{  @$settings -> mobile  }}"
                                                                   id="mobile"
                                                                   class="form-control"
                                                                   placeholder="رقم التواصل مع الاكاديمية  "
                                                                   name="mobile">
                                                            @error('mobile')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">

                                                            <input type="hidden" id="latitudef" name="latitude"
                                                                   value="{{@$settings -> latitude}}">
                                                            <input type="hidden" id="longitudef" name="longitude"
                                                                   value="{{@$settings -> longitude}}">

                                                            <input type="text" id="pac-input" name="latLng"
                                                                   class='form-control  controls {{$errors->has('latLng') ? 'redborder' : ''}}'>

                                                            <label for="name_ar"> ابحث عن موقعك <span
                                                                    class="astric">*</span></label>
                                                            <small
                                                                class="text-danger">{{ $errors->has('latLng') ? $errors->first('latLng') : '' }}</small>
                                                            <br>

                                                            <div class="error-messagen">
                                                                @if($errors->has('latitude'))
                                                                    {{$errors -> first('latitude')}}
                                                                @endif
                                                            </div>

                                                            <div id="map" style="height: 500px;width: 1000px;"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <h4 class="form-section"><i class="ft-user"></i>من نحن </h4>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> عنوان صفحة من نحن
                                                                بالعربي </label>
                                                            <input type="text" value="{{ @$settings -> title_ar }}"
                                                                   id="title_ar"
                                                                   class="form-control"
                                                                   placeholder="ادخل  العنوان  باللغة العربية "
                                                                   name="title_ar">
                                                            @error('title_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> عنوان صفحة من نحن
                                                                بالانجليزي </label>
                                                            <input type="text" value="{{  @$settings -> title_en  }}"
                                                                   id="title_en"
                                                                   class="form-control"
                                                                   placeholder="ادخل   العنوان  باللغة  الانجليزية  "
                                                                   name="title_en">
                                                            @error('title_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> المحتوي بالعربي </label>
                                                            <textarea id="ckeditor-language" name="content_ar" rows="10"
                                                                      name="content_ar">{{  @$settings -> content_ar  }}</textarea>
                                                            @error('content_ar')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="projectinput1"> المحتوي بالانجليزي </label>
                                                            <textarea id="ckeditor-language2" name="content_en"
                                                                      rows="10"
                                                                      name="content_en">{{@$settings -> content_en }}</textarea>
                                                            @error('content_en')
                                                            <span class="text-danger"> {{$message}}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-actions">
                                                    <button type="button" class="btn btn-warning mr-1"
                                                            onclick="history.back();">
                                                        <i class="ft-x"></i> تراجع
                                                    </button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="la la-check-square-o"></i> حفظ
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic form layout section end -->
            </div>
        </div>
    </div>
@stop


@section('script')
    <script>

        CKEDITOR.replace('ckeditor-language', {
            extraPlugins: 'language',
            // Customizing list of languages available in the Language drop-down.
            language_list: ['ar:Arabic:rtl', 'fr:French', 'he:Hebrew:rtl', 'es:Spanish'],
            height: 350
        });

        CKEDITOR.replace('ckeditor-language2', {
            extraPlugins: 'language',
            // Customizing list of languages available in the Language drop-down.
            language_list: ['ar:Arabic:rtl', 'fr:French', 'he:Hebrew:rtl', 'es:Spanish'],
            height: 350
        });


        let lat  = $('#latitudef').val();
        let lng = $('#longitudef').val();
        $('#pac-input').val('{{@$settings -> address}}');

        if($('#latitudef').val() !== "" && $('#longitudef').val() !== ""){
            prevlat = lat;
            prevLng = lng;
        }else{
            prevlat =24.694970;
            prevLng =46.724130;
        }

        function initAutocomplete() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: parseInt(prevlat)  , lng:  parseInt(prevLng) },
                zoom: 13,
                mapTypeId: 'roadmap'
            });

            // move pin and current location
            infoWindow = new google.maps.InfoWindow;
            geocoder = new google.maps.Geocoder();
            // move pin and current location
            var markers = [];

            if($('#latitudef').val() === "" && $('#longitudef').val() === ""){
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(pos),
                            map: map,
                            zoom: 13,
                            title: 'موقعك الحالي'
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            geocodeLatLng(geocoder, map, infoWindow, marker);
                        });
                        // to get current position address on load
                        google.maps.event.trigger(marker, 'click');
                    }, function () {
                        handleLocationError(true, infoWindow, map.getCenter());
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infoWindow, map.getCenter());
                } if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        var pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };
                        map.setCenter(pos);
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(pos),
                            map: map,
                            zoom: 13,
                            title: 'موقعك الحالي'
                        });
                        markers.push(marker);
                        marker.addListener('click', function () {
                            geocodeLatLng(geocoder, map, infoWindow, marker);
                        });
                        // to get current position address on load
                        google.maps.event.trigger(marker, 'click');
                    }, function () {
                        handleLocationError(true, infoWindow, map.getCenter());
                    });
                } else {
                    // Browser doesn't support Geolocation
                    handleLocationError(false, infoWindow, map.getCenter());
                }
            }else{
                deleteMarkers();
                var location = new google.maps.LatLng(lat,lng);
                var marker = new google.maps.Marker({
                    position: location,
                    map: map
                });
                infoWindow.setContent("{{@$settings -> address ? @$settings -> address  :  'موقعك  الحالي '}}");
                infoWindow.open(map, marker);
                markers.push(marker);
                map.setCenter(location);
            }
            var geocoder = new google.maps.Geocoder();
            google.maps.event.addListener(map, 'click', function(event) {
                SelectedLatLng = event.latLng;
                geocoder.geocode({
                    'latLng': event.latLng
                }, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        if (results[0]) {
                            deleteMarkers();
                            addMarkerRunTime(event.latLng);
                            SelectedLocation = results[0].formatted_address;
                            console.log( results[0].formatted_address);
                            splitLatLng(String(event.latLng));
                            $("#pac-input").val(results[0].formatted_address);
                        }
                    }
                });
            });
            function geocodeLatLng(geocoder, map, infowindow,markerCurrent) {
                var latlng = {lat: markerCurrent.position.lat(), lng: markerCurrent.position.lng()};
                /* $('#branch-latLng').val("("+markerCurrent.position.lat() +","+markerCurrent.position.lng()+")");*/
                // $('#latitudef').val(markerCurrent.position.lat());
                //   $('#longitudef').val(markerCurrent.position.lng());

                geocoder.geocode({'location': latlng}, function(results, status) {
                    if (status === 'OK') {
                        if (results[0]) {
                            map.setZoom(8);
                            var marker = new google.maps.Marker({
                                position: latlng,
                                map: map
                            });
                            markers.push(marker);
                            infowindow.setContent(results[0].formatted_address);
                            SelectedLocation = results[0].formatted_address;
                            // $("#pac-input").val(results[0].formatted_address);

                            infowindow.open(map, marker);
                        } else {
                            window.alert('No results found');
                        }
                    } else {
                        window.alert('Geocoder failed due to: ' + status);
                    }
                });
                SelectedLatLng =(markerCurrent.position.lat(),markerCurrent.position.lng());
            }
            function addMarkerRunTime(location) {
                var marker = new google.maps.Marker({
                    position: location,
                    map: map
                });
                markers.push(marker);
            }
            function setMapOnAll(map) {
                for (var i = 0; i < markers.length; i++) {
                    markers[i].setMap(map);
                }
            }
            function clearMarkers() {
                setMapOnAll(null);
            }
            function deleteMarkers() {
                clearMarkers();
                markers = [];
            }


            // Create the search box and link it to the UI element.
            var input = document.getElementById('pac-input');
            //  $("#pac-input").val("أبحث هنا ");
            var searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(input);

            // Bias the SearchBox results towards current map's viewport.
            map.addListener('bounds_changed', function() {
                searchBox.setBounds(map.getBounds());
            });

            // Listen for the event fired when the user selects a prediction and retrieve
            // more details for that place.
            searchBox.addListener('places_changed', function() {
                var places = searchBox.getPlaces();

                if (places.length == 0) {
                    return;
                }

                // Clear out the old markers.
                markers.forEach(function(marker) {
                    marker.setMap(null);
                });
                markers = [];

                // For each place, get the icon, name and location.
                var bounds = new google.maps.LatLngBounds();
                places.forEach(function(place) {
                    if (!place.geometry) {
                        console.log("Returned place contains no geometry");
                        return;
                    }
                    var icon = {
                        url: place.icon,
                        size: new google.maps.Size(100, 100),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(17, 34),
                        scaledSize: new google.maps.Size(25, 25)
                    };

                    // Create a marker for each place.
                    markers.push(new google.maps.Marker({
                        map: map,
                        icon: icon,
                        title: place.name,
                        position: place.geometry.location
                    }));


                    $('#latitudef').val(place.geometry.location.lat());
                    $('#longitudef').val(place.geometry.location.lng());

                    if (place.geometry.viewport) {
                        // Only geocodes have viewport.
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });
        }
        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
            infoWindow.open(map);
        }
        function splitLatLng(latLng){
            var newString = latLng.substring(0, latLng.length-1);
            var newString2 = newString.substring(1);
            var trainindIdArray = newString2.split(',');
            var lat = trainindIdArray[0];
            var Lng  = trainindIdArray[1];

            $("#latitudef").val(lat);
            $("#longitudef").val(Lng);
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDKZAuxH9xTzD2DLY2nKSPKrgRi2_y0ejs&libraries=places&callback=initAutocomplete&language=ar&region=SA
         async defer"></script>
@stop
