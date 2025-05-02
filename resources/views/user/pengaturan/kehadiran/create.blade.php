<head>
  
</head>

<style>
    #map {
      height: 400px;
      display: none; /* Hide the map by default */
    }

    .form-check .working-days-box {
        margin-left: 4px; /* Adjust this value as needed */
    }
  </style>

<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true" data-bs-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addJadwalModalLabel">Pengaturan Absensi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="formAddJadwal" class="needs-validation form-lbl-dot" novalidate autocomplete="off">
        <input type="hidden" id="attendance_id" name="attendance_id" value="">
        <input type="hidden" id="isEdit" name="isEdit" value="0">
        <div class="modal-body">
          <div class="mb-3" style="display: none;" id="excodeattendance-container">
            <label for="excodeattendance" class="title-case-jadwal">Kode Absensi</label>
            <input type="text" class="form-control" id="excodeattendance" name="excodeattendance" disabled>
          </div>
          <div class="mb-3">
            <label for="description" class="title-case-jadwal lbl-req">Deskripsi</label>
            <input type="text" class="form-control" id="description" name="description" >
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
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col">
              <div class="form-text duration-helper-text text-muted">
                Masukan dalam menit untuk Toleransi Telat. contoh 00:45 untuk 45 menit
              </div>
            </div>
          </div>
          <div class="row mb-3">
            <div class="mb-1" id="option-container">
              <div class="col mb-2">
                <label for="optionEmployee" class="title-case-jadwal lbl-req">Dituju Kepada</label>
              </div>
              <select class="form-select" style="width: 100%;" id="optionEmployee" name="optionEmployee" required>
                <option value="" disabled selected>Pilih Target Karyawan</option>
                <option value="allEmployee">Semua Karyawan</option>
                <option value="specificEmployee">Karyawan Tertentu</option>
              </select>
            </div>
            <div class="mb-1 mt-1" id="karyawan-container" style="display: none;">
              <label class="col-sm-5 title-case-jadwal mb-2" for="jadwalEmployee">Karyawan</label>
              <select multiple style="width: 100%;" name="jadwalEmployee[]" id="jadwalEmployee" class="form-control" data-placeholder="-:Pilih Karyawan:-"></select>
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
          @if(in_array('UPLOAD_GAMBAR', request()->get('permission_codes')))
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="checkInPhoto" name="checkInPhoto">
            <label class="form-check-label title-case-jadwal" for="checkInPhoto">Masuk Dengan Foto</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="breakPhoto" name="breakPhoto">
            <label class="form-check-label title-case-jadwal" for="breakPhoto">Istirahat Dengan Foto</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="checkOutPhoto" name="checkOutPhoto">
            <label class="form-check-label title-case-jadwal" for="checkOutPhoto">Keluar Dengan Foto</label>
          </div>
          @endif
          <div class="mb-3" id="active-container">
            <label class="title-case-jadwal lbl-req" id="labelAktif" style="margin-right: 15px;">Apakah jadwal ini aktif?</label>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeTrue" value="activeTrue" checked>
              <label class="form-check-label title-case-jadwal" for="activeTrue">Ya</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="activeOption" id="activeFalse" value="activeFalse">
              <label class="form-check-label title-case-jadwal" for="activeFalse">Tidak</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-sm btn-warning">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
    let map, marker, manualMode = false;
    let watchId; // Variable to store the watch position ID

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

    function initMap() {
      const mapOptions = {
        zoom: 15,
      };

      if(document.getElementById("map")) {
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
      }

      // Show the map container
      $("#map").show();
      $("#map-container").show();
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
      initMap();

      // Toggle manual mode when the button is clicked
      manualMode = !manualMode;

      if (!manualMode) {
        // Toggle manual mode off
        google.maps.event.clearListeners(map, "click");
        destroyMap();
      }
    });

    let currentMenuId = "{{request()->get('menu_id')}}";
    let selectedEmployeeId = [];
    let deletedEmployeeId = [];

    $("#optionEmployee").select2({
      dropdownParent: $("#addJadwalModal #formAddJadwal"),
    });
    $("select[name='optionEmployee']").change(function() {
      const selectedOption = $(this).val();

      if (selectedOption === "specificEmployee") {
        $("#karyawan-container").slideDown();
      } else {
        $("#karyawan-container").slideUp();
      }
    });

    $("#jadwalEmployee").select2({
		  dropdownParent: $("#addJadwalModal #formAddJadwal"),
      // tags: true,
			ajax: {
				url: `{{route('user.page.karyawan.select')}}?menu_id=${currentMenuId}`,
				data: function (params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function (data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.karyawan_id;
						item.text = item.karyawan_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
      },
		})
    
    $("#jadwalEmployee").on("select2:select", function(e) {
        const selectedId = parseInt(e.params.data.id);

        const deletedIndex = deletedEmployeeId.indexOf(selectedId);
        if (deletedIndex > -1) {
            deletedEmployeeId.splice(deletedIndex, 1);
        }

        if (!selectedEmployeeId.includes(selectedId)) {
            selectedEmployeeId.push(selectedId);
        }

        console.log("selected", selectedEmployeeId);
        console.log("deleted", deletedEmployeeId);
    });

    $("#jadwalEmployee").on("select2:unselect", function(e) {
        const unselectedId = parseInt(e.params.data.id);

        const selectedIndex = selectedEmployeeId.indexOf(unselectedId);
        if (selectedIndex > -1) {
            selectedEmployeeId.splice(selectedIndex, 1);
        }

        if ($("#isEdit").value === '1' && !deletedEmployeeId.includes(unselectedId)) {
            deletedEmployeeId.push(unselectedId);
        }

        console.log("selected", selectedEmployeeId);
        console.log("deleted", deletedEmployeeId);
    });

    $("#useBreaktime").click(function() {
      const isBreaktimeUsed = $(this).prop("checked");

      if (isBreaktimeUsed) {
        $("#breaktimeForms").slideDown();
      } else {
        $("#breaktimeForms").slideUp();
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

    function resetAndSlideUpForms() {
      resetMap();
      selectedEmployeeId = [];
      deletedEmployeeId = [];
      $(".error-message").remove();
      $("#formAddJadwal")[0].reset();
      $("#breaktimeForms").slideUp();
      $("#excodeattendance-container").slideUp();
      $("#locationForms").slideUp();
      $("#specificTimeForm").slideUp();
      $("#durationForm").slideUp();
      $("#karyawan-container").slideUp();
      $("#isEdit").val('0');
      $("#attendance_id").val('');
      $("#jadwalEmployee").empty().trigger("change"); // Remove all options from the select2 dropdown
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

    function resetMap() {
      // Remove the existing map marker
      if (marker) {
        marker.setMap(null);
      }

      // Reset latitude, longitude, and company address inputs
      $("#latitude").val('');
      $("#longitude").val('');
      $("#companyAddress").val('');

      // Hide the map
      $("#map").hide();
      $("#map-container").hide();
    }

    $("#addJadwalModal").on("hidden.bs.modal", function () {
      resetAndSlideUpForms()// Reset the form fields when the modal is closed
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
          jsonData["attendance_end_break"] = item.value;
        } else if (item.name === "durationtime" && item.value !== "") {
          jsonData["attendance_end_break"] = item.value;
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
        } else if(item.name === "optionEmployee") {
          jsonData["attendance_is_all"] = item.value === 'allEmployee' ? true : false
        }
        

        // Set the CSRF token in the JSON data
        jsonData["_token"] = csrfToken;
      }); 

      if(jsonData["attendance_start_break"] && jsonData["attendance_end_break"] && jsonData["attendance_break_type"] === "DURATION") {
        const newTime = addTime(jsonData["attendance_start_break"], jsonData["attendance_end_break"])

        jsonData["attendance_end_break"] = newTime
      }

      let hasErrors = false;

      if(!jsonData["attendance_description"]) {
        appendError($("#description"), "Mohon isi deskripsi.");
        hasErrors = true;
      }

      jsonData["isEdit"] = $("#isEdit").val() === '1' ? true : false;
      jsonData["attendance_employee"] = selectedEmployeeId
      jsonData["attendance_deleted_employee"] = deletedEmployeeId
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
          if(!jsonData["attendance_end_break"]) {
            appendError($("#specificTime"), "Mohon masukan Waktu Spesifik.");
            hasErrors = true;
          } else {
            const checkInTime = jsonData["attendance_end_break"];

            // Validate the format of the check-in time
            const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
            if (!timePattern.test(checkInTime)) {
              appendError($("#specificTime"), "Format jam harus 24 Jam.");
              hasErrors = true;
            }
          }
        } else if(jsonData["attendance_break_type"] === 'DURATION') {
          if(!jsonData["attendance_end_break"]) {
            appendError($("#durationtime"), "Mohon masukan Durasi Waktu.");
            hasErrors = true;
          } else {
            const checkInTime = jsonData["attendance_end_break"];

            // Validate the format of the check-in time
            const timePattern = /^(?:[01]\d|2[0-3]):(?:[0-5]\d)$/;
            if (!timePattern.test(checkInTime)) {
              appendError($("#durationtime"), "Format jam harus 24 Jam.");
              hasErrors = true;
            }
          }
        }
      }

      if (hasErrors) {
        $(".spinner-box").fadeOut();
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
          $(".spinner-box").fadeOut();
          $(".error-message").remove();
          if (response.success) {
            toastr.success(response.message);
            resetAndSlideUpForms()
            // isEdit = false
            $("#addJadwalModal").modal("hide");
          } else {
            toastr.error(response.message);
          }
        },
        error: function(xhr, status, error) {
          $(".spinner-box").fadeOut();
          toastr.error("An error occurred while submitting the form.");
          console.log(xhr.responseText);
        }
      });
    });

    $("#formAddJadwal").keypress(function(event) {
      // Check if the pressed key is Enter (key code 13)
      if (event.which === 13) {
        // Prevent the default form submission action
        event.preventDefault();
      }
    });

    $("#addJadwalModal").on("click", ".btn-close", function(e) {
      e.preventDefault();

      $("#isEdit").val('0');
      $("#attendance_id").val('');
      resetAndSlideUpForms()
    })
    
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

    setHtmlTitle('{{$title}}')
  });
</script>
