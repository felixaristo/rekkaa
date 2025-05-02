
<style>
    #map {
      height: 400px;
      display: none; /* Hide the map by default */
    }

    .form-check .working-days-box {
        margin-left: 4px; /* Adjust this value as needed */
    }
</style>

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="mb-4">Pengaturan Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddJadwal" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="attendance_id" name="attendance_id" value="">
        <input type="hidden" id="isEdit" name="isEdit" value="0">
        <div class="card-body mt-4">
          <div class="mb-3" style="display: none;" id="excodeattendance-container">
            <label for="excodeattendance" class="title-case-jadwal">Kode Absensi</label>
            <input type="text" class="form-control" id="excodeattendance" name="excodeattendance" disabled>
          </div>
          <div class="mb-3" style="display: none;">
            <label for="description" class="title-case-jadwal lbl-req">Deskripsi</label>
            <input type="text" class="form-control" value="Absensi" id="description" name="description" >
          </div>
          <div class="mb-3">
            <label for="workingDays" class="title-case-jadwal lbl-req">Hari Kerja</label>
            <div class="working-days-box">
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDayMonday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Monday" id="workingDayMonday" name="workingDays[]">Senin</label>
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDayTuesday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Tuesday" id="workingDayTuesday" name="workingDays[]">Selasa</label>
                
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDayWednesday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Wednesday" id="workingDayWednesday" name="workingDays[]">Rabu</label>
                
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDayThursday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Thursday" id="workingDayThursday" name="workingDays[]">Kamis</label>
                
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDayFriday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Friday" id="workingDayFriday" name="workingDays[]">Jumat</label>
               
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                
                <label for="workingDaySaturday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Saturday" id="workingDaySaturday" name="workingDays[]">Sabtu</label>
              </div>
              <div class="form-check" style="margin-right: 1rem; font-size: 12px">
                <label for="workingDaySunday" class="day-label" style="color: #697A8D;"><input class="form-check-input" type="checkbox" value="Sunday" id="workingDaySunday" name="workingDays[]">Minggu</label>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <label for="workingHours" class="title-case-jadwal">Jam Kerja</label>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="start" class="title-case-jadwal lbl-req">Mulai</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="start" name="start"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                <span class="icon"><i class="fas fa-clock"></i></span>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="end" class="title-case-jadwal lbl-req">Berakhir</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="end" name="end"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                <span class="icon"><i class="fas fa-clock"></i></span>
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <label for="lateTolerance" class="title-case-jadwal lbl-req">Toleransi Telat</label>
            </div>
            <div class="col">
              <div class="input-container">
                <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="lateTolerance" name="lateTolerance"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                <span class="icon"><i class="fas fa-clock"></i></span>
                <span class="form-text duration-helper-text text-muted">
                Masukan dalam menit untuk Toleransi Telat. contoh 00:45 untuk 45 menit
                </span>
              </div>
            </div>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="useBreaktime" name="useBreaktime">
            <label class="form-check-label title-case-jadwal" for="useBreaktime">Gunakan Istirahat</label>
          </div>
          <div id="breaktimeForms" style="display: none;">
            <div class="row mb-3">
              <div class="col">
                <label for="startBreak" class="title-case-jadwal lbl-req">Mulai Istirahat</label>
              </div>
              <div class="col">
                <div class="input-container">
                  <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="startBreak" name="startBreak"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                  <span class="icon"><i class="fas fa-clock"></i></span>
                </div>
              </div>
            </div>
            <div class="row mb-3">
              <div class="col">
                <label class="title-case-jadwal lbl-req" id="labelSelesai">Selesai Istirahat</label>
              </div>
              <div class="col">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="endBreakOption" id="specificTimeOption" value="specificTime">
                  <label class="form-check-label title-case-jadwal" for="specificTimeOption">Waktu Spesifik</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="endBreakOption" id="durationOption" value="duration">
                  <label class="form-check-label title-case-jadwal" for="durationOption">Durasi</label>
                </div>
              </div>
            </div>
            <div id="specificTimeForm" style="display: none;">
              <div class="row mb-3">
                <div class="col">
                  <label for="specificTime" class="title-case-jadwal lbl-req">Waktu Spesifik</label>
                </div>
                <div class="col">
                  <div class="input-container">
                    <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="specificTime" name="specificTime"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                    <span class="icon"><i class="fas fa-clock"></i></span>
                  </div>
                </div>
              </div>
            </div>
            <div id="durationForm" style="display: none;">
              <div class="row mb-3">
                <div class="col">
                  <label for="duration" class="title-case-jadwal lbl-req">Durasi</label>
                </div>
                <div class="col">
                  <div class="input-container">
                    <input type="text" class="form-control without_ampm" pattern="([0-1]{1}[0-9]{1}|20|21|22|23):[0-5]{1}[0-9]{1}" id="durationtime" name="durationtime"  placeholder="--:--" maxlength="5" oninput="formatTimeInput(this)">
                    <span class="icon"><i class="fas fa-clock"></i></span>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col">
                    <div class="form-text duration-helper-text text-muted">
                      Masukan dalam menit untuk Durasi Istirahat. contoh 00:45 untuk 45 menit
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @if(in_array('TRACK_LOCATION', request()->get('permission_codes')))
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="useLocation" name="useLocation">
            <label class="form-check-label title-case-jadwal" for="useLocation">Gunakan Lokasi Kerja</label>
          </div>
          <div id="locationForms" style="display: none;">
            <button type="button" id="buttonLocation" style="position: relative;" class="mb-3 btn btn-sm btn-warning">
              Pilih Lokasi 📍
            </button>
            <!-- <div class="dropdown mb-2">
              <input type="text" class="form-control" id="mapSearch" placeholder="Cari Lokasi Kerja" width="75%">
              <div class="dropdown-content" id="addressList"></div>
            </div> -->
            <div class="searchMapContainer mb-2" id="map-container" style="display: none;">
              <div style="display: flex; width: 100%;">
                <input type="text" class="form-control" id="mapSearch" placeholder="Cari Lokasi Kerja" style="width: 90%;">
                <button type="button" id="searchButton" class="btn btn-sm btn-warning" style="width: 10%; margin-left: 1rem;"><i class="bx bx-search-alt"></i></button>
              </div>
            </div>
            <div id="map" class="mb-3">
            
            </div>
            <div class="form-group textbox-address mb-3">
              <label for="companyAddress" class="col-sm-12 col-title-case-jadwal lbl-req">Lokasi Kerja</label>
              <textarea name="companyAddress"  id="companyAddress" class="form-control" readonly></textarea>
            </div>
            <div class="mb-3">
              <label for="latitude" class="title-case-jadwal lbl-req">Latitude</label>
              <input type="text" class="form-control" id="latitude" name="latitude" readonly>
            </div>
            <div class="mb-3">
              <label for="longitude" class="title-case-jadwal lbl-req">Longitude</label>
              <input type="text" class="form-control" id="longitude" name="longitude" readonly>
            </div>
          </div>
          @endif
          @if(in_array('UPLOAD_IMAGE', request()->get('permission_codes')))
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="checkInPhoto" name="checkInPhoto">
            <label class="form-check-label title-case-jadwal" for="checkInPhoto">Masuk Dengan Foto</label>
          </div>
          <div class="form-check mb-3" id="breakPhotoCheckbox" style="display: none;">
            <input class="form-check-input" type="checkbox" id="breakPhoto" name="breakPhoto">
            <label class="form-check-label title-case-jadwal" for="breakPhoto">Istirahat Dengan Foto</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="checkOutPhoto" name="checkOutPhoto">
            <label class="form-check-label title-case-jadwal" for="checkOutPhoto">Keluar Dengan Foto</label>
          </div>
          @endif
          <div class="row mb-3" id="active-container">
            <div class="col">
              <label class="title-case-jadwal lbl-req" id="labelGunakanAbsensi" style="margin-right: 15px;">Gunakan Fitur Absensi?</label>
            </div>
            <div class="col">
              <select class="form-select" style="width: 100%;" id="optionGunakanAbensi" name="optionGunakanAbensi" required>
                <option value="" disabled selected>Pilih Gunakan Fitur Absensi</option>
                <option value="1">Ya</option>
                <option value="0">Tidak</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" id="btn-simpan" class="btn btn-sm btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  function formatTimeInput(input) {
      if (input.value.length === 2 && !input.value.includes(":")) {
          input.value += ":";
      }
      if (input.value.length > 5) {
          input.value = input.value.slice(0, 5);
      }
  }

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    


    let map, marker, manualMode = false;
    let watchId; // Variable to store the watch position ID

    
    $('#formAddJadwal [type=reset]').click();

    let data = @json($data);

    if(data !== null) {
      populateEditJadwalModal(data)
    }
    
    function formatTimeToHHmm(time) {
      const timeParts = time.split(":");
      return timeParts[0] + ":" + timeParts[1];
    }
    // $("#mapSearch").on('input', function() {
    //   searchAddressesList();
    // });

    $("#searchButton").on('click', function () {
      searchAddressesList();
    });

    function searchAddressesList() {
      const geocoder = new google.maps.Geocoder();
      const mapSearchInput = document.getElementById("mapSearch");
      const address = mapSearchInput.value;

      // Set componentRestrictions to Indonesia
      const componentRestrictions = {
        country: "ID" // Country code for Indonesia
      };

      geocoder.geocode({ address: address, componentRestrictions: componentRestrictions }, function (results, status) {
        if (status === "OK") {
          // Update the map center to the first result's location
          if (results.length > 0) {
            const mapOptions = {
              zoom: 15,
              center: results[0].geometry.location // Set the center to the first result's location
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            // Add a marker for the selected location
            if (marker) {
              marker.setMap(null); // Remove existing marker
            }

            marker = new google.maps.Marker({
              position: results[0].geometry.location,
              map: map,
              draggable: true // Allow marker to be draggable
            });

            // Update form fields with the selected address information
            updateFormFields(results[0]);

            // Add event listeners for marker and map
            addMarkerEventListeners();
          }
        } else {
          alert("Geocode was not successful due to: " + status);
        }
      });
    }

    function addMarkerEventListeners() {
      // Event listener for marker dragend
      google.maps.event.addListener(marker, "dragend", function (event) {
        handleMarkerEvent(event.latLng);
      });

      // Event listener for map click
      google.maps.event.addListener(map, "click", function (event) {
        handleMarkerEvent(event.latLng);
      });
    }

    function handleMarkerEvent(location) {
      // Update latitude and longitude inputs
      marker.setPosition(location);
      $("#latitude").val(location.lat());
      $("#longitude").val(location.lng());

      // Reverse geocode to get the address
      const geocoder = new google.maps.Geocoder();
      geocoder.geocode({ location: location }, function (results, status) {
        if (status === "OK") {
          if (results[0]) {
            const address = results[0].formatted_address;
            $("#companyAddress").val(address);
          }
        }
      });
    }
    
    function updateFormFields(result) {
      // Update other form fields with the selected address information
      const latitudeInput = $("#latitude");
      const longitudeInput = $("#longitude");
      const companyAddressInput = $("#companyAddress");

      latitudeInput.val(result.geometry.location.lat());
      longitudeInput.val(result.geometry.location.lng());
      companyAddressInput.val(result.formatted_address);
    }

    function placeMarker(event) {
      let location;

      // Check if event is from clicking on the map
      if (event.latLng) {
        location = event.latLng;
      } else if (event.lat && event.lng) {
        // If not, check if lat and lng properties are available
        location = {
          lat: () => event.lat,
          lng: () => event.lng
        };
      } else {
        // Handle the case where neither latLng nor lat/lng is available
        console.error("Invalid event object:", event);
        return;
      }

      if (marker) {
        marker.setPosition(location);
      } else {
        marker = new google.maps.Marker({
          position: location,
          map: map,
          draggable: true,
        });
      }

      const geocoder = new google.maps.Geocoder();
      const latitudeInput = document.getElementById("latitude");
      const longitudeInput = document.getElementById("longitude");

      // Clear the watch position if it's already active
      if (watchId) {
        navigator.geolocation.clearWatch(watchId);
      }

      // Set the inputs with the updated latitude and longitude
      latitudeInput.value = isFunction(location, 'lat') ? location.lat() : location.lat;
      longitudeInput.value = isFunction(location, 'lng') ? location.lng() : location.lng;

      // Reverse geocode to get the address
      geocoder.geocode({ location: location }, function (results, status) {
        if (status === "OK") {
          if (results[0]) {
            const address = results[1].formatted_address;
            document.getElementById("companyAddress").value = address;
          }
        }
      });

      google.maps.event.addListener(map, "click", function (event) {
        const clickedLocation = event.latLng;

        // Update marker position
        marker.setPosition(clickedLocation);

        $("#latitude").val(clickedLocation.lat());
        $("#longitude").val(clickedLocation.lng());

        // Reverse geocode to get the address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: clickedLocation }, function (results, status) {
          if (status === "OK") {
            if (results[0]) {
              const address = results[1].formatted_address;
              document.getElementById("companyAddress").value = address;
            }
          }
        });
      });

      google.maps.event.addListener(marker, "dragend", function (event) {
        const draggedLocation = event.latLng;

        // Update latitude and longitude inputs
        marker.setPosition(draggedLocation);

        $("#latitude").val(draggedLocation.lat());
        $("#longitude").val(draggedLocation.lng());

        // Reverse geocode to get the address
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: draggedLocation }, function (results, status) {
          if (status === "OK") {
            if (results[0]) {
              const address = results[0].formatted_address;
              $("#companyAddress").val(address);
            }
          }
        });
      });
    }

    // let marker;

    function initMap(latitude, longitude) {
      const mapOptions = {
        zoom: 15,
        center: {
          lat: latitude,
          lng: longitude
        }
      };

      if(document.getElementById("map")) {
        map = new google.maps.Map(document.getElementById("map"), mapOptions);
        // Create a marker with the initial position
        marker = new google.maps.Marker({
          position: {
            lat: latitude,
            lng: longitude
          },
          map: map,
          draggable: true,
        });

        // Add an event listener to update latitude and longitude on map click
        google.maps.event.addListener(map, "click", function(event) {
          const clickedLocation = event.latLng;

          // Update marker position
          marker.setPosition(clickedLocation);

          // Update latitude and longitude inputs
          $("#latitude").val(clickedLocation.lat());
          $("#longitude").val(clickedLocation.lng());

          // Reverse geocode to get the address
          const geocoder = new google.maps.Geocoder();
          geocoder.geocode({
            location: clickedLocation
          }, function(results, status) {
            if (status === "OK") {
              if (results[0]) {
                const address = results[1].formatted_address;
                $("#companyAddress").val(address);
              }
            }
          });
        });

        // Add an event listener to update latitude, longitude, and address on marker drag
        google.maps.event.addListener(marker, "dragend", function(event) {
          const draggedLocation = event.latLng;

          // Update latitude and longitude inputs
          $("#latitude").val(draggedLocation.lat());
          $("#longitude").val(draggedLocation.lng());

          // Reverse geocode to get the address
          const geocoder = new google.maps.Geocoder();
          geocoder.geocode({
            location: draggedLocation
          }, function(results, status) {
            if (status === "OK") {
              if (results[0]) {
                const address = results[1].formatted_address;
                $("#companyAddress").val(address);
              }
            }
          });
        });
      }
    }


    function isFunction(obj, propName) {
      return typeof obj[propName] === 'function';
    }

    function destroyMap() {
      // You might need additional cleanup steps based on your requirements
      if (map) {
        google.maps.event.clearListeners(map, 'click');
        map = null;
      }
    }

    $("#buttonLocation").on("click", function () {
      // initMap(false,false);
      const mapOptions = {
        zoom: 15,
      };

      map = new google.maps.Map(document.getElementById("map"), mapOptions);

      // Try HTML5 geolocation
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function (position) {
            const initialLocation = {
              lat: position.coords.latitude,
              lng: position.coords.longitude
            };

            map.setCenter(initialLocation);
            placeMarker({ latLng: initialLocation }); // Simulate the event with the initial location
          },
          function () {
            // Handle errors (e.g., user denied location access)
            console.error("Error getting the current location");
          }
        );
      } else {
        // Browser doesn't support Geolocation
        console.error("Browser doesn't support Geolocation");
      }

      // Show the map container
      $("#map").show();
      $("#map-container").show();

      // Toggle manual mode when the button is clicked
      manualMode = !manualMode;

      if (!manualMode) {
        // Toggle manual mode off
        google.maps.event.clearListeners(map, "click");
        destroyMap();
      }

      $("#buttonLocation").hide()
    });

    function subtractTime(time1, time2) {
      // Parse the time strings and extract hours and minutes
      const [hours1, minutes1] = time1.split(':').map(Number);
      const [hours2, minutes2] = time2.split(':').map(Number);

      // Calculate the total minutes for each time
      const totalMinutes1 = hours1 * 60 + minutes1;
      const totalMinutes2 = hours2 * 60 + minutes2;

      // Perform the subtraction
      const differenceMinutes = totalMinutes1 - totalMinutes2;

      // Calculate hours and minutes for the result
      const resultHours = Math.floor(differenceMinutes / 60);
      const resultMinutes = differenceMinutes % 60;

      // Format the result as "HH:mm"
      const result = `${String(resultHours).padStart(2, '0')}:${String(resultMinutes).padStart(2, '0')}`;
      return result;
    }

    $("input[name='endBreakOption']").change(function() {
      const selectedOption = $(this).val();

      if (selectedOption === "specificTime") {
        $("#specificTimeForm").slideDown();
        $("#durationForm").slideUp();
      } else if (selectedOption === "duration") {
        $("#specificTimeForm").slideUp();
        $("#durationForm").slideDown();
      }
    });

    function populateEditJadwalModal(rowData) {
      if (rowData.attendance_status_active === true) {
        $("#activeTrue").prop("checked", true);
      } else {
        $("#activeFalse").prop("checked", true);
      }

      // $("#excodeattendance-container").slideDown();
      $("#excodeattendance").val('AB' + rowData.attendance_id);

      // Update the form fields with the rowData
      $("#attendance_id").val(rowData.attendance_id);
      $("#isEdit").val('1');

      // console.log($("#isEdit").val(), 'isEdit 2')

      $("#description").val(rowData.attendance_description);
      // Populate other form fields similarly

      if (rowData.attendance_working_day.length !== 0 && rowData.attendance_working_day !== "") {
        const workingDay = JSON.parse(rowData.attendance_working_day)
        workingDay.forEach(function(day) {
          $(`input[name="workingDays[]"][value="${day}"]`).prop("checked", true);
        });
      }

      $("#start").val(formatTimeToHHmm(rowData.attendance_check_in));
      $("#end").val(formatTimeToHHmm(rowData.attendance_check_out));
      $("#attendance_id").val(rowData.attendance_id);
      if (rowData.attendance_check_in_tolerance) {
        $("#lateTolerance").val(formatTimeToHHmm(rowData.attendance_check_in_tolerance));
      }

      $("#companyAddress").val(rowData.attendance_location_address);

      if (rowData.attendance_location_latitude) {
        $("#latitude").val(rowData.attendance_location_latitude);
      }

      if (rowData.attendance_location_longitude) {
        $("#longitude").val(rowData.attendance_location_longitude);
      }

      // Set the breaktime related fields based on the data
      if (rowData.attendance_break_status) {
        $("#useBreaktime").prop("checked", true);
        $("#breakPhotoCheckbox").show();
        $("#breaktimeForms").slideDown();
        $("#startBreak").val(formatTimeToHHmm(rowData.attendance_start_break))
      } else {
        $("#useBreaktime").prop("checked", false);
        $("#breaktimeForms").slideUp();
        $("#breakPhotoCheckbox").hide();
      }

      if (rowData.attendance_location_status) {
        $("#useLocation").prop("checked", true);
        $("#locationForms").slideDown();
      } else {
        $("#useLocation").prop("checked", false);
        $("#locationForms").slideUp();
      }

      // console.log('rowData.attendance_break_type', rowData.attendance_break_type)
      if (rowData.attendance_break_type === "SPECIFIC") {
        $("#specificTimeOption").prop("checked", true);
        $("#durationOption").prop("checked", false);
        $("#specificTimeForm").slideDown();
        $("#durationForm").slideUp();
        $("#specificTime").val(formatTimeToHHmm(rowData.attendance_end_break));
      } else if (rowData.attendance_break_type === "DURATION") {
        $("#specificTimeOption").prop("checked", false);
        $("#durationOption").prop("checked", true);
        $("#specificTimeForm").slideUp();
        $("#durationForm").slideDown();
        $("#durationtime").val(formatTimeToHHmm(subtractTime(rowData.attendance_end_break, rowData.attendance_start_break)));
      } else {
        $("#specificTimeOption").prop("checked", false);
        $("#durationOption").prop("checked", false);
        $("#specificTimeForm").slideUp();
        $("#durationForm").slideUp();
      }

      // Set the checkInPhoto and checkOutPhoto checkboxes based on the data
      $("#checkInPhoto").prop("checked", rowData.attendance_check_in_status_photo);
      $("#checkOutPhoto").prop("checked", rowData.attendance_check_out_status_photo);
      $("#breakPhoto").prop("checked", rowData.attendance_break_status_photo);

      if (rowData.attendance_location_status && rowData.attendance_location_latitude && rowData.attendance_location_longitude) {
        initMap(
          parseFloat(rowData.attendance_location_latitude),
          parseFloat(rowData.attendance_location_longitude)
        );

        
        $("#buttonLocation").hide();

        $("#map").show();
        $("#map-container").show();
      }

      $("#optionGunakanAbensi").val(rowData.attendance_is_used).trigger("change");

      $("#btn-simpan").text("Perbarui");
    }

    $("#useBreaktime").click(function() {
      const isBreaktimeUsed = $(this).prop("checked");

      if (isBreaktimeUsed) {
        $("#breaktimeForms").slideDown();
        $("#breakPhotoCheckbox").show();
      } else {
        $("#breaktimeForms").slideUp();
        $("#breakPhotoCheckbox").hide();
      }
    });

    $("#useLocation").click(function() {
      const isBreaktimeUsed = $(this).prop("checked");

      if (isBreaktimeUsed) {
        $("#locationForms").slideDown();
      } else {
        $("#locationForms").slideUp();
      }
    });

    function addTime(time1, time2) {
      const [hours1, minutes1] = time1.split(':').map(Number);
      const [hours2, minutes2] = time2.split(':').map(Number);

      const totalMinutes1 = hours1 * 60 + minutes1;
      const totalMinutes2 = hours2 * 60 + minutes2;

      const sumMinutes = totalMinutes1 + totalMinutes2;

      const resultHours = Math.floor(sumMinutes / 60);
      const resultMinutes = sumMinutes % 60;

      const result = `${String(resultHours).padStart(2, '0')}:${String(resultMinutes).padStart(2, '0')}`;
      return result;
    }

    $("#formAddJadwal").submit(function(event) {
      event.preventDefault();
      $(".spinner-box").css({'display': 'table'});
      $(".error-message").remove();

      const csrfToken = $('meta[name="csrf-token"]').attr('content');

      const formData = $(this).serializeArray();
      const jsonData = {}; // To store the JSON data

      formData.forEach(function(item) {
        if(item.name === "description") {
          jsonData["attendance_description"] = item.value
        } else if (item.name === "workingDays[]") {
          if (!jsonData["attendance_working_day"]) jsonData["attendance_working_day"] = [];
          jsonData["attendance_working_day"].push(item.value);
        } else if(item.name === "start") {
          jsonData["attendance_check_in"] = item.value
        } else if(item.name === "end") {
          jsonData["attendance_check_out"] = item.value
        } else if(item.name === "lateTolerance") {
          jsonData["attendance_check_in_tolerance"] = item.value === '' ? null : item.value 
        } else if(item.name === "companyAddress") {
          jsonData["attendance_location_address"] = item.value
        } else if(item.name === "latitude") {
          jsonData["attendance_location_latitude"] = item.value === '' ? null : item.value
        } else if(item.name === "longitude") {
          jsonData["attendance_location_longitude"] = item.value === '' ? null : item.value
        } else if (item.name === "useBreaktime") {
          jsonData["attendance_break_status"] = item.value === "on";
        } else if (item.name === "startBreak" && item.value !== '') {
          jsonData["attendance_start_break"] = item.value;
        } else if (item.name === "endBreakOption") {
          if(item.value === 'specificTime') {
            jsonData["attendance_break_type"] = "SPECIFIC";
          } else if(item.value === 'duration') {
            jsonData["attendance_break_type"] = "DURATION";
          } else {
            jsonData["attendance_break_type"] = null
          }
        } else if (item.name === "specificTime" && item.value !== "") {
          jsonData["attendance_end_break_spesific"] = item.value;
        } else if (item.name === "durationtime" && item.value !== "") {
          jsonData["attendance_end_break_duration"] = item.value;
        } else if (item.name === "checkInPhoto") {
          jsonData["attendance_check_in_status_photo"] = item.value === "on";
        } else if (item.name === "checkOutPhoto") {
          jsonData["attendance_check_out_status_photo"] = item.value === "on";
        } else if (item.name === 'attendance_id' && item.value !== '') {
          jsonData["attendance_id"] = item.value
        } else if (item.name === "breakPhoto") {
          jsonData["attendance_break_status_photo"] = item.value === "on";
        } else if (item.name === "activeOption") {
          if(item.value === 'activeTrue') {
            jsonData["attendance_status_active"] = true;
          } else {
            jsonData["attendance_status_active"] = false;
          }
        } else if (item.name === "useLocation") {
          jsonData["attendance_location_status"] = item.value === "on";
        } else if (item.name === "optionGunakanAbensi") {
          jsonData["attendance_is_used"] = item.value;
        }
        

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      }); 

      if(jsonData["attendance_start_break"] && jsonData["attendance_end_break"] && jsonData["attendance_break_type"] === "DURATION") {
        const newTime = addTime(jsonData["attendance_start_break"], jsonData["attendance_end_break"])

        jsonData["attendance_end_break"] = newTime
      }

      let hasErrors = false;
      let isbottom = false;
      if(!jsonData["attendance_description"]) {
        appendError($("#description"), "Mohon isi deskripsi.");
        hasErrors = true;
      }

      if(!jsonData["attendance_is_used"]) {
        appendError($("#optionGunakanAbensi").next(".select2-container"), "Mohon isi Gunakan Absensi.");
        hasErrors = true;
        isbottom = true;
      }

      jsonData["isEdit"] = $("#isEdit").val() === '1' ? true : false;
      console.log(jsonData, 'jsondata')

      if(!jsonData["attendance_check_in"]) {
        appendError($("#start"), "Mohon masukan jam Masuk.");
        hasErrors = true;
      } else {
        const checkInTime = jsonData["attendance_check_in"];

        // Validate the format of the check-in time
        const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
        if (!timePattern.test(checkInTime)) {
          appendError($("#start"), "Format jam harus 24 Jam.");
          hasErrors = true;
        }
      }

      if(!jsonData["attendance_check_out"]) {
        appendError($("#end"), "Mohon masukan jam Keluar.");
        hasErrors = true;
      } else {
        const checkInTime = jsonData["attendance_check_out"];

        // Validate the format of the check-in time
        const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
        if (!timePattern.test(checkInTime)) {
          appendError($("#end"), "Format jam harus 24 Jam.");
          hasErrors = true;
        }
      }

      if(jsonData["attendance_location_status"] === true) {
        if(!jsonData["attendance_location_longitude"]) {
          appendError($("#longitude"), "Mohon masukan Longitude Perusahaan.");
          hasErrors = true;
        }

        if(!jsonData["attendance_location_latitude"]) {
          appendError($("#latitude"), "Mohon masukan Latitude Perusahaan.");
          hasErrors = true;
        }
      }

      if(jsonData["attendance_break_status"] === true) {
        if(!jsonData["attendance_start_break"]) {
          appendError($("#startBreak"), "Mohon masukan jam Mulai Istirahat.");
          hasErrors = true;
        }

        if(jsonData["attendance_break_type"] === 'SPECIFIC') {
          if(!jsonData["attendance_end_break_spesific"]) {
            appendError($("#specificTime"), "Mohon masukan Waktu Spesifik.");
            hasErrors = true;
          } else {
            jsonData["attendance_end_break"] = jsonData["attendance_end_break_spesific"];
            const checkInTime = jsonData["attendance_end_break"];

            // Validate the format of the check-in time
            const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
            if (!timePattern.test(checkInTime)) {
              appendError($("#specificTime"), "Format jam harus 24 Jam.");
              hasErrors = true;
            }
          }
        } else if(jsonData["attendance_break_type"] === 'DURATION') {
          if(!jsonData["attendance_end_break_duration"]) {
            appendError($("#durationtime"), "Mohon masukan Durasi Waktu.");
            hasErrors = true;
          } else {
            // jsonData["attendance_end_break"] = jsonData["attendance_end_break_duration"];
            const checkInTime = jsonData["attendance_end_break_duration"];

            // Validate the format of the check-in time
            const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
            if (!timePattern.test(checkInTime)) {
              appendError($("#durationtime"), "Format jam harus 24 Jam.");
              hasErrors = true;
            } else {
              jsonData["attendance_end_break"] = addTimes(jsonData["attendance_start_break"], jsonData["attendance_end_break_duration"])
            }
          }
        }
      }

      if (hasErrors) {
        $(".spinner-box").fadeOut();
        if(!isbottom) {
          $('html, body').animate({
              scrollTop: 400
          }, 'fast');
        }
        
        isbottom = false;
        return false;
      }

      if(!jsonData["attendance_working_day"]) {
        $(".error-message").remove();
        $(".spinner-box").fadeOut();
        Swal.fire({
					html: 'Mohon pilih hari minimal satu hari kerja!',
					confirmButtonText: "Ok",
					showCancelButton: false,
					icon: 'error'
				})
        return false
      }

      if(jsonData["attendance_break_status"] === true && !jsonData["attendance_break_type"]) {
        $(".error-message").remove();
        $(".spinner-box").fadeOut();
        Swal.fire({
          html: 'Mohon pilih salah satu opsi Istirahat!',
          confirmButtonText: "Ok",
          showCancelButton: false,
          icon: 'error'
        })
        return false
      }

      $.ajax({
        type: "POST",
        url: `{{ route('user.page.pengaturan.kehadiran.save') }}?menu_id=${currentMenuId}`,
        data: JSON.stringify(jsonData),
        contentType: "application/json",
        dataType: "json",
        success: function(response) {
          $(".error-message").remove();
          if (response.success) {
            toastr.success(response.message);
            
            $("#btn-simpan").text("Perbarui");
            // $("#excodeattendance-container").slideDown();
            $("#excodeattendance").val('AB' + response.data.attendance_id);

            // Update the form fields with the response
            $("#attendance_id").val(response.data.attendance_id);
            // resetAndSlideUpForms()
            // isEdit = false
            // $("#addJadwalModal").modal("hide");
          } else {
            toastr.error(response.message);
          }
          $(".spinner-box").fadeOut();
        },
        error: function(xhr, status, error) {
          $(".spinner-box").fadeOut();
          toastr.error("An error occurred while submitting the form.");
          console.log(xhr.responseText);
        }
      });
    });

    function addTimes(startTime, duration) {
        const [startHours, startMinutes] = startTime.split(":").map(Number);
        const [durationHours, durationMinutes] = duration.split(":").map(Number);

        let totalHours = startHours + durationHours;
        let totalMinutes = startMinutes + durationMinutes;

        if (totalMinutes >= 60) {
            totalHours += Math.floor(totalMinutes / 60);
            totalMinutes %= 60;
        }

        // Format the result
        const resultHours = String(totalHours).padStart(2, "0");
        const resultMinutes = String(totalMinutes).padStart(2, "0");

        return `${resultHours}:${resultMinutes}`;
    }

    $("#formAddJadwal").keypress(function(event) {
      // Check if the pressed key is Enter (key code 13)
      if (event.which === 13) {
        // Prevent the default form submission action
        event.preventDefault();
      }
    });

    function appendError($inputElement, errorMessage) {
      // Add the "error" class to the form-group container
      $inputElement.addClass("error");

      // Create the error message element
      $errorElement = $("<div>")
        .addClass("error-message")
        .addClass("error-text")
        .text(errorMessage);

      // Insert the error message after the form-group container
      $inputElement.after($errorElement);
    }

    $("input").focus(function() {
      $(this).removeClass("error");
      $(this).next(".error-message").remove();
    });

    $("#optionGunakanAbensi").select2();

    setHtmlTitle('{{$title}}')
  });
</script>