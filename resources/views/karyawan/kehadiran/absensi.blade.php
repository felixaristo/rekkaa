<style>
  .fixed-width-button {
    width: 150px; /* Adjust the width as per your preference */
  }
</style>

<div class="row">
    <div class="col-lg-12 mb-3 order-0">
        <!-- Bootstrap Table with Header - Light -->
        <div class="card">
          <div class="card-body">
              <!-- <div class="d-flex justify-content-between align-items-start">
           
              </div> -->
              <div class="d-flex flex-column align-items-start justify-content-center">
                <h5 class="mb-4">Rekam Absensi</h5>
              </div>
              <div class="d-flex justify-content-center mt-2 d-none d-sm-flex" id="absensi-desktop">
                  <button type="button" class="btn button-modal btn-orange mx-2 rounded-pill fixed-width-button" value="Masuk">Masuk</button>
                  <button type="button" class="btn button-modal btn-orange mx-2 rounded-pill fixed-width-button" value="Mulai Istirahat" style="display: none;">Mulai Istirahat</button>
                  <button type="button" class="btn button-modal btn-orange mx-2 rounded-pill fixed-width-button" value="Selesai Istirahat" style="display: none;">Selesai Istirahat</button>
                  <button type="button" class="btn button-modal btn-orange mx-2 rounded-pill fixed-width-button" value="Keluar">Keluar</button>
              </div>

              <div class="d-md-none" id="absensi-mobile">
                <div class="col-md-3 col-sm-12 text-center mb-2">
                  <button type="button" class="btn button-modal btn-orange rounded-pill fixed-width-button" value="Masuk">Masuk</button>
                </div>
                <div class="col-md-3 col-sm-12 text-center mb-2">
                  <button type="button" class="btn button-modal btn-orange rounded-pill fixed-width-button" value="Mulai Istirahat" style="display: none;">Mulai Istirahat</button>
                </div>
                <div class="col-md-3 col-sm-12 text-center mb-2">
                  <button type="button" class="btn button-modal btn-orange rounded-pill fixed-width-button" value="Selesai Istirahat" style="display: none;">Selesai Istirahat</button>
                </div>
                <div class="col-md-3 col-sm-12 text-center mb-2">
                  <button type="button" class="btn button-modal btn-orange rounded-pill fixed-width-button" value="Keluar">Keluar</button>
                </div>
              </div>
          </div>
        </div>
        <!-- Bootstrap Table with Header - Light -->
    </div>
</div>

<!-- Add the camera view section as a modal -->
<div class="modal fade" id="cameraModal" data-bs-focus="false" tabindex="-1" role="dialog" aria-labelledby="cameraModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cameraModalLabel">Masuk</h5>
        <!-- Remove the close button (X) in the modal header -->
      </div>
      <div class="modal-body">
        <!-- Create a container for the camera view -->
        <div class="camera-container">
          <video id="cameraStream" autoplay></video>
          <!-- Add an element to display the live time inside the camera view -->
          <div class="live-time-container">
            <div id="liveTime"></div>
            <div id="addressLocation"></div>
          </div>
        </div>
        <!-- Add a container for the list of attendance data -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-sm btn-warning">Rekam Absen</button>
        <button type="button" class="btn btn-sm btn-secondary custom-tutup" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  var selectedAttendanceId = null;
  var attendanceData = null;
  var timeTextElement = document.getElementById('liveTime');
  var addressElement = document.getElementById('addressLocation');
  var userId = {!! json_encode(session()->get('karyawan_data')['karyawan_id']) !!};

  var cameraStreamObj = null;
  var canvas = document.createElement('canvas');
  var context = canvas.getContext('2d');
  var videoElement = document.getElementById('cameraStream');
  var googleMapsApiKey = @json(env('GOOGLE_MAPS_API_KEY'));
  window.googleMapsApiKey = googleMapsApiKey;
  var intervalId = null;
  var newAction = 'Masuk';
  var isOpen = false;
  var isLocation = false;
  var formData = new FormData();

  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";
    setHtmlTitle('{{$title}}')
    
    let latitude, longitude;
    let checkKaryawan = null;

    $('.button-modal').click(function() {
      openCameraModal($(this).val()); // Pass the value of the clicked button to openCameraModal
    });

    async function fetchAttendanceData() {
      try {
        const data = await fetchJson(`/karyawan/kehadiran/list-jadwal?menu_id=${currentMenuId}`);
        return data.data;
      } catch (error) {
        console.error('Error fetching attendance data:', error);
        return [];
      }
    }

    $('.button-modal:contains("Mulai Istirahat")').hide();
    $('.button-modal:contains("Selesai Istirahat")').hide();

    async function fetchKaryawanData(karyawan_id) {
      try {
        var customDate = new Date();

        var year = customDate.getFullYear();
        var month = customDate.getMonth() + 1; // Month is 0-indexed, so add 1
        var day = customDate.getDate();

        var formattedDate = year + "-" + (month < 10 ? "0" : "") + month + "-" + (day < 10 ? "0" : "") + day;

        const response = await fetch(`/karyawan/kehadiran/detail-karyawan/${formattedDate}/${karyawan_id}?menu_id=${currentMenuId}`); //22 will change to number of karyawan after karyawan has Id on user
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        const data = await response.json();
        return data;
      } catch (error) {
        return [];
      }
    }  

    $(document).ready(async function() {
      $(".spinner-box").css({ display: "table" });
      attendanceData = await fetchAttendanceData();
      // Fetch Karyawan data and check conditions
      const getKaryawan = await fetch(`/karyawan/kehadiran/check-karyawan/${userId}?menu_id=${currentMenuId}`);
      if (!getKaryawan.ok) {
        throw new Error('Network response was not ok');
      }
      const dataKaryawan = await getKaryawan.json();
      
      selectedAttendanceId = dataKaryawan.data.st_attendance_id
    
      selectedAttendance = attendanceData.find(
        (attendance) => attendance.attendance_id === parseInt(selectedAttendanceId)
      );

      if (selectedAttendance && selectedAttendance.attendance_break_status) {
        $('.button-modal:contains("Mulai Istirahat")').show();
        $('.button-modal:contains("Selesai Istirahat")').show();
      }
      console.log('selectedAttendance init', selectedAttendance);

      $(".spinner-box").fadeOut()
    });

    // Function to handle the change event when the attendance dropdown value changes
    function handleAttendanceDropdownChange() {
      const selectElement = $('#attendanceListContainer').find('.form-control');
      selectedAttendanceId = selectElement.val(); // Update the selectedAttendanceId when the dropdown value changes

      selectedAttendance = attendanceData.find(
        (attendance) => attendance.attendance_id === parseInt(selectedAttendanceId)
      );
      console.log('selectedAttendance dropdown', selectedAttendance);
    }
  
    var capturePhotoButton = document.querySelector('.btn-warning');
    capturePhotoButton.addEventListener('click', () => capturePhoto(newAction));

    
    async function openCameraModal(action) {
      $(".spinner-box").css({ display: "table" });

      formData = new FormData(); // reset form data
      attendanceData = await fetchAttendanceData();
      newAction = action;

      const getKaryawan = await fetch(`/karyawan/kehadiran/check-karyawan/${userId}?menu_id=${currentMenuId}`);

      if (!getKaryawan.ok) throw new Error('Network response was not ok');

      const dataKaryawan = await getKaryawan.json();
      

      const showMessage = (icon, html) => {
        Swal.fire({
          showCancelButton: false,
          confirmButtonText: "Ok",
          icon: 'error',
          html: html
        });
        $(".spinner-box").fadeOut();
      };

      if (dataKaryawan.data === null) {
        showMessage('error', 'Anda tidak terdaftar sebagai karyawan');
        return;
      }

      if (dataKaryawan.data.st_attendance_id === null) {
        showMessage('error', 'Anda belum di daftarkan pada setting Absensi');
        return;
      }


      const checkErrorConditions = (conditions) => {
        for (const condition of conditions) {
          if (condition[0]) {
            showMessage('error', condition[1]);
            return true;
          }
        }
        return false;
      };

      selectedAttendanceId = dataKaryawan.data.st_attendance_id;
      let selectedAttendance = attendanceData.find(
        attendance => attendance.attendance_id === parseInt(selectedAttendanceId)
      );

      console.log('selectedAttendance open camera', selectedAttendance);

      const checkHoliday = await fetch(`{{ route("karyawan.kehadiran.check.date") }}?menu_id=${currentMenuId}`)
      .then(response => response.json())
      .then(data => {
        return data
      })
      .catch(error => {
        console.error('Error fetching checkHoliday data:', error);
      });

       // Assuming checkHoliday data is an array

      const currentDate = new Date();

      const daysOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
      const selectedAttendanceDays = JSON.parse(selectedAttendance.attendance_working_day);
      const todayDay = daysOfWeek[currentDate.getDay()];

      if (!selectedAttendanceDays.includes(todayDay) || checkHoliday.data.length > 0) {
        showMessage('error', 'Tidak dapat absen di Hari Libur / diluar Hari Kerja');
        return
      }

      if((action === 'Mulai Istirahat' || action === 'Selesai Istirahat') && selectedAttendance.attendance_break_status_photo === true) {
        isOpen = true
      } else if (action === 'Keluar' && selectedAttendance.attendance_check_out_status_photo === true) {
        isOpen = true
      } else if (action === 'Masuk' && selectedAttendance.attendance_check_in_status_photo === true) {
        isOpen = true
      } else {
        isOpen = false
      }

      if(selectedAttendance.attendance_location_status === true) {
        isLocation = true
      } 

      checkKaryawan = await fetchKaryawanData(dataKaryawan.data.karyawan_id);

      // console.log(checkKaryawan, 'checkkaryawan');
      // console.log(checkKaryawan.length, 'checkkaryawan length');

      if (checkErrorConditions([
          [newAction === 'Masuk' && checkKaryawan.length !== 0, 'Anda sudah melakukan Absen Masuk hari ini'],
          [newAction === 'Mulai Istirahat' && checkKaryawan.length === 0, 'Anda perlu melakukan Absen Masuk terlebih dahulu'],
          [newAction === 'Mulai Istirahat' && checkKaryawan.length !== 0 && checkKaryawan.attendancekaryawan_break_start !== null, 'Anda tidak dapat Mulai Istirahat lagi untuk hari ini'],
          [newAction === 'Selesai Istirahat' && checkKaryawan.length === 0, 'Anda perlu melakukan Absen Masuk terlebih dahulu'],
          [newAction === 'Selesai Istirahat' && checkKaryawan.length !== 0 && checkKaryawan.attendancekaryawan_break_start === null, 'Anda perlu Mulai Istirahat terlebih dahulu'],
          [newAction === 'Selesai Istirahat' && checkKaryawan.length !== 0 && checkKaryawan.attendancekaryawan_break_end !== null, 'Anda tidak dapat Selesai Istirahat lagi untuk hari ini'],
          [newAction === 'Keluar' && checkKaryawan.length === 0, 'Anda perlu melakukan Absen Masuk terlebih dahulu'],
          [newAction === 'Keluar' && selectedAttendance.attendance_break_status === true  && (checkKaryawan.attendancekaryawan_break_start === null || checkKaryawan.attendancekaryawan_break_end === null), 'Anda perlu absen Istirahat terlebih dahulu'],
          [newAction === 'Keluar' && checkKaryawan.length !== 0 && checkKaryawan.attendancekaryawan_check_out !== null, 'Anda sudah melakukan Absen Keluar hari ini'],
      ])) return;

      if (isOpen) {  
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ video: true });
          videoElement.srcObject = stream;
          cameraStreamObj = stream;
        } catch (error) {
          const errorMessageElement = document.createElement('div');
          errorMessageElement.className = 'alert alert-danger';
          errorMessageElement.textContent = 'Camera access denied. Please allow camera access to proceed.';
          document.body.appendChild(errorMessageElement);
          setTimeout(() => {
            errorMessageElement.style.display = 'none';
          }, 5000);
        }
      }

      timeTextElement.textContent = timeText = formatDateTime(new Date());

      intervalId = setInterval(updateLiveTime, 1000);

      if (isLocation === true && !addressFetched) {
        addressFetched = true;
        await fetchAndDisplayLocation();
        const [addressText, timeText] = [
          document.getElementById('addressLocation').textContent,
          document.getElementById('liveTime').textContent
        ];

        if (timeText) {
          $('#cameraModal').modal('show');     
          $('#cameraModal').on('shown.bs.modal', () => $(".spinner-box").fadeOut());
        }
      } else {
        addressFetched = true
        if (timeText) {
          $('#cameraModal').modal('show');     
          $('#cameraModal').on('shown.bs.modal', () => $(".spinner-box").fadeOut());
        }
      }

      const modalTitleElement = document.getElementById('cameraModalLabel');
      modalTitleElement.textContent = action;

      $('#cameraModal').on('hidden.bs.modal', handleCloseCameraModal);

      const closeCameraButton = document.querySelector('.btn-secondary');
      closeCameraButton.addEventListener('click', handleCloseCameraModal);
    }

    async function capturePhoto(action) {
      try {
        if (captureInProgress) return $(".spinner-box").fadeOut();

        let canvas

        if(isOpen === false) {
          canvas = document.createElement('canvas');
          const canvasWidth = 640; // Set your desired canvas width
          const canvasHeight = 480; // Set your desired canvas height
          canvas.width = canvasWidth;
          canvas.height = canvasHeight;
        } else {
          canvas = await createCanvasFromVideo();
        }

        const context = canvas.getContext('2d');
        
        // const { latitude, longitude } = await getCurrentPosition();

        const [addressText, timeText] = [
          document.getElementById('addressLocation').textContent,
          document.getElementById('liveTime').textContent
        ];

        const selectedAttendance = attendanceData.find(
          attendance => attendance.attendance_id === parseInt(selectedAttendanceId)
        );

        console.log('selectedAttendance capture photo', selectedAttendance);

        drawCanvasWithText(context, addressText, timeText, canvas);
        formData.append('image', await getBlobFromCanvas(canvas), 'photo.png');

        // if (!timeText) {
        //   const errorMsg = `Anda perlu menunggu Live Time muncul sebelum merekam absen.`;
        //   await Swal.fire({ title: 'Error', text: errorMsg, icon: 'error' });
        //   return handleCloseCameraModal();
        // } else if (isLocation === true & !addressText) {
        //   const errorMsg = `Anda perlu menunggu Live Location muncul sebelum merekam absen.`;
        //   await Swal.fire({ title: 'Error', text: errorMsg, icon: 'error' });
        //   return handleCloseCameraModal();
        // }

        formData.append('attendancekaryawan_time', timeText);
        formData.append('attendancekaryawan_location_address', addressText ? addressText : '-');
        formData.append('st_attendance_id', selectedAttendanceId);


        const typeMap = {
          'Masuk': {
            type: 'CHECKIN',
            action: async () => {
              formData.append('attendancekaryawan_type', 'CHECKIN');
              if(isLocation === true) {
                const selectedCoords = getSelectedCoordinates(selectedAttendance);
                const distanceInMeters = getDistanceToSelectedCoordinates(latitude, longitude, selectedCoords);
                console.log(distanceInMeters)

                if (distanceInMeters > 500) {
                  const swalResult = await showInputModal('Anda tidak berada di area kantor. Mohon masukkan alasan', 'Mohon masukkan alasan anda', 'attendancekaryawan_check_in_note');
                  if (swalResult.isConfirmed) {
                    handleSwalConfirmedAction(swalResult);
                  }
                } else if(selectedAttendance.attendance_check_in < timeText.split(' '[1])) {
                  const swalResult = await showInputModal('Anda telat melakukan absen masuk. Mohon masukan alasan.', 'Mohon masukkan alasan anda', 'attendancekaryawan_check_in_late_note');
                  if (swalResult.isConfirmed) {
                    handleSwalConfirmedAction(swalResult);
                  }
                } else {
                  await submitAttendanceForm();
                }
              } else if(selectedAttendance.attendance_check_in < timeText.split(' '[1])) {
                const swalResult = await showInputModal('Anda telat melakukan absen masuk. Mohon masukan alasan.', 'Mohon masukkan alasan anda', 'attendancekaryawan_check_in_late_note');
                if (swalResult.isConfirmed) {
                  handleSwalConfirmedAction(swalResult);
                }
              } else {
                await submitAttendanceForm(); 
              }
              
            }
          },
          'Keluar': {
            type: 'CHECKOUT',
            action: async () => {
              formData.append('attendancekaryawan_type', 'CHECKOUT');
              if (selectedAttendance.attendance_check_out > timeText.split(' ')[1]) {
                const swalResult = await showInputModal('Anda melakukan absen keluar sebelum waktunya. Mohon beri alasan.', 'Mohon masukkan alasan anda.', 'attendancekaryawan_check_out_note');
                if (swalResult.isConfirmed) handleSwalConfirmedAction(swalResult);
              } else {
                await submitAttendanceForm();
              }
            }
          },
          'Mulai Istirahat': {
            type: 'STARTBREAK',
            action: async () => {
              formData.append('attendancekaryawan_type', 'STARTBREAK');
              const isInvalidBreakTime = selectedAttendance.attendance_start_break !== timeText.split(' ')[1];
              if (isInvalidBreakTime) {
                const swalResult = await showInputModal('Jam istirahat tidak sesuai dengan yang ditetapkan. Mohon masukan alasan', 'Mohon masukkan alasan anda', 'attendancekaryawan_break_start_note');
                if (swalResult.isConfirmed) handleSwalConfirmedAction(swalResult);
              } else {
                await submitAttendanceForm();
              }
            }
          },
          'Selesai Istirahat': {
            type: 'ENDBREAK',
            action: async () => {
              formData.append('attendancekaryawan_type', 'ENDBREAK');
              if (selectedAttendance.attendance_break_type === 'SPECIFIC') {
                if(selectedAttendance.attendance_end_break < timeText.split(' ')[1]) {
                  const swalResult = await showInputModal('Waktu istirahatmu melebihi jam yang ditentukan. Mohon masukan alasan anda', 'Mohon masukkan alasan anda', 'attendancekaryawan_break_end_note');
                  if (swalResult.isConfirmed) handleSwalConfirmedAction(swalResult);
                } else {
                  await submitAttendanceForm();
                }
              } else if (selectedAttendance.attendance_break_type === 'DURATION') {
                const duration = subtractTime(selectedAttendance.attendance_end_break, selectedAttendance.attendance_start_break);
                const currentDuration = subtractTime(timeText.split(' ')[1], checkKaryawan.attendancekaryawan_break_start);
                if (currentDuration > duration) {
                  const swalResult = await showInputModal('Waktu istirahatmu melebihi jam yang ditentukan. Mohon masukan alasan anda', 'Mohon masukkan alasan anda', 'attendancekaryawan_break_end_note');
                  if (swalResult.isConfirmed) {}handleSwalConfirmedAction(swalResult);
                } else {
                  await submitAttendanceForm();
                }
              } else {
                $(".spinner-box").fadeOut();
                Swal.fire({
                  title: 'Error',
                  text: 'Kantor anda tidak memerlukan Absen Istirahat',
                  icon: 'error',
                  showCancelButton: false,
                  confirmButtonColor: '#3085d6',
                  confirmButtonText: 'OK'
                });
              }
            }
          }
        };

        const actionDetails = typeMap[newAction];
        if (actionDetails) {
          const { type, action } = actionDetails;
          formData.append('attendancekaryawan_type', type);
          await action();
        }
      } catch (error) {
        handleError(error);
      }
    }

    function handleCloseCameraModal() {
      if (cameraStreamObj && cameraStreamObj.getTracks) {
        cameraStreamObj.getTracks().forEach((track) => {
          track.stop();
        });
      }

      clearInterval(intervalId);

      const closeCameraButton = document.querySelector('.btn-secondary');
      closeCameraButton.removeEventListener('click', handleCloseCameraModal)

      $(".spinner-box").fadeOut();

      // Hide the camera modal
      $('#cameraModal').modal('hide');
    }

    function fillTextWrap(context, text, x, y, maxWidth, lineHeight) {
      const words = text.split(' ');
      var line = '';

      for (var i = 0; i < words.length; i++) {
          const testLine = line + words[i] + ' ';
          const metrics = context.measureText(testLine);
          const testWidth = metrics.width;

          if (testWidth > maxWidth && i > 0) {
              context.fillText(line, x, y);
              line = words[i] + ' ';
              y += lineHeight;
          } else {
              line = testLine;
          }
      }

      context.fillText(line, x, y);
    }

    var captureInProgress = false;

    function dmsToDecimal(degrees, minutes, seconds, direction) {
      var dd = degrees + minutes / 60 + seconds / 3600;
      return direction === "S" || direction === "W" ? -dd : dd;
    }
    
    function isDMSFormat(value) {
        // Define a regular expression pattern to match DMS format
        const pattern = /^-?\d{1,3}°\s*\d{1,2}'\s*\d{1,2}"\s*[NSEW]$/i;

        // Use the test() method to check if the value matches the pattern
        return pattern.test(value);
    }

    function handleSwalConfirmedAction(swalResult) {
      const result = swalResult.value;
      if (result === false) {
        // Handle error or failed request
        Swal.fire({
          title: 'Gagal melakukan Absen',
          confirmButtonText: 'Ok',
          type: 'error',
        });
      } else {
        captureInProgress = false;
        return handleCloseCameraModal();
      }
    }

    async function showInputModal(htmlText, validationMessage, formDataKey) {

      const swalResult = await Swal.fire({
        html: htmlText,
        input: 'text',
        showCancelButton: true,
        allowOutsideClick: false,
        confirmButtonText: 'Oke',
        cancelButtonText: 'Tidak',
        reverseButtons: true,
        inputValidator: value => (!value ? validationMessage : undefined),
        preConfirm: async reason => {
          Swal.showLoading();
          formData.append(formDataKey, reason);
          try {
            if(htmlText === 'Anda tidak berada di area kantor. Mohon masukkan alasan') {
              if(selectedAttendance.attendance_check_in < timeText.split(' '[1])) {
                await Swal.fire({
                  html: 'Anda telat melakukan absen masuk. Mohon masukan alasan.',
                  input: 'text',
                  showCancelButton: true,
                  allowOutsideClick: false,
                  confirmButtonText: 'Oke',
                  cancelButtonText: 'Tidak',
                  reverseButtons: true,
                  inputValidator: value => (!value ? 'Mohon masukan alasan anda.' : undefined),
                  preConfirm: async reason => {
                    Swal.showLoading();
                    formData.append('attendancekaryawan_check_in_late_note', reason);
                    try {
                      return await submitAttendanceForm();
                    } catch (error) {
                      Swal.showValidationMessage(`Request failed: ${error}`);
                      return false;
                    }
                  },
                  allowOutsideClick: () => false,
                });
              }
            } else {
              return await submitAttendanceForm();
            }
          } catch (error) {
            Swal.showValidationMessage(`Request failed: ${error}`);
            return false;
          }
        },
        allowOutsideClick: () => false,
      });
      return swalResult;
    }

    function handleError(error) {
      captureInProgress = false;
      console.error('Error capturing photo:', error);
      $('#cameraModal').on('hidden.bs.modal', handleCloseCameraModal);
      Swal.fire({ title: 'Error', text: 'Gagal melakukan Absen', icon: 'error', showCancelButton: false, });
      handleCloseCameraModal();
    }

    async function submitAttendanceForm() {
      try {
        $(".spinner-box").css({ display: "table" });
        const response = await fetch(`{{ route("karyawan.kehadiran.check-in") }}?menu_id=${currentMenuId}`, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: formData,
        });

        if (!response.ok) throw new Error(response.statusText);

        const result = await response.json();
        if (!result.success) throw new Error(result.message);

        toastr.success(result.message);
        captureInProgress = false;
        $(".spinner-box").fadeOut();
        return handleCloseCameraModal();
      } catch (error) {
        handleError(error);
      }
    }

    function getDistanceBetweenCoordinates(lat1, lon1, lat2, lon2) {
      const earthRadius = 6371000; // Earth's radius in meters

      const toRadians = (degrees) => (degrees * Math.PI) / 180;
      const deltaLat = toRadians(lat2 - lat1);
      const deltaLon = toRadians(lon2 - lon1);

      const a = Math.sin(deltaLat / 2) ** 2 +
        Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) *
        Math.sin(deltaLon / 2) ** 2;

      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

      return earthRadius * c;
    }

    function getSelectedCoordinates(selectedAttendance) {
      let selectedLatitude = parseFloat(selectedAttendance.attendance_location_latitude);
      let selectedLongitude = parseFloat(selectedAttendance.attendance_location_longitude);

      if (isDMSFormat(selectedLatitude)) {
        selectedLatitude = dmsToDecimal(getDMSParts(selectedLatitude));
      }

      if (isDMSFormat(selectedLongitude)) {
        selectedLongitude = dmsToDecimal(getDMSParts(selectedLongitude));
      }

      return { latitude: selectedLatitude, longitude: selectedLongitude };
    }

    function getDMSParts(dmsValue) {
      const dmsParts = dmsValue.match(/([0-9.]+)°\s*([NSEW])/);
      return [parseFloat(dmsParts[1]), dmsParts[2]];
    }

    function getDistanceToSelectedCoordinates(lat, lon, selectedCoords) {
      return getDistanceBetweenCoordinates(lat, lon, selectedCoords.latitude, selectedCoords.longitude);
    }

    function decimalToDMS(decimal) {
      const degrees = Math.floor(decimal);
      const minutesDecimal = (decimal - degrees) * 60;
      const minutes = Math.floor(minutesDecimal);
      const seconds = Math.round((minutesDecimal - minutes) * 60);
      return `${degrees}° ${minutes}' ${seconds}"`;
    }

    function createCanvasFromVideo() {
      const canvas = Object.assign(document.createElement('canvas'), {
        width: videoElement.videoWidth,
        height: videoElement.videoHeight
      });
      canvas.getContext('2d').drawImage(videoElement, 0, 0, canvas.width, canvas.height);
      return canvas;
    }

    function drawCanvasWithText(context, addressText, timeText, canvas) {
      const textMargin = 20, maxWidth = canvas.width - 2 * textMargin, lineHeight = 25;
      context.font = '20px Arial';
      context.fillStyle = context.strokeStyle = 'orange';
      fillTextWrap(context, addressText, textMargin, textMargin + 40, maxWidth, lineHeight);
      fillTextWrap(context, timeText, textMargin, textMargin + 15, maxWidth, lineHeight);
      context.lineWidth = 4;
      context.strokeRect(0, 0, canvas.width, canvas.height);
    }

    async function getBlobFromCanvas(canvas) {
      return new Promise((resolve) => canvas.toBlob(resolve));
    }

    function formatDateTime(date) {
      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, '0');
      const day = String(date.getDate()).padStart(2, '0');
      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');
      const seconds = String(date.getSeconds()).padStart(2, '0');

      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    }

    var addressFetched = false;

    async function fetchAndDisplayLocation() {
      try {
        if(isLocation === false) {
          addressElement.textContent = ''
        } else {
          await getCurrentPosition();
          const response = await fetch(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${googleMapsApiKey}`);
          const data = await response.json();
          console.log(data)
          if (data.error_message) addressElement.textContent = 'Error: ' + data.error_message;
          else if (data.results && data.results.length > 0) addressElement.textContent = data.results[1].formatted_address;
          else addressElement.textContent = 'Address not found';
        }
      } catch (error) {
        console.error('Error fetching location:', error);
        addressElement.textContent = 'Error fetching location';
      }
    }

    async function getCurrentPosition() {
      if (isLocation === true) {
        return new Promise((resolve, reject) => {
          if (navigator.geolocation) {
            const options = {
              enableHighAccuracy: true,
              // timeout: 10000, // Timeout in milliseconds
              maximumAge: 0, // Maximum age of cached position
            };

            // Use watchPosition to continuously monitor the user's location
            const watchId = navigator.geolocation.watchPosition(
              (position) => resolve(
                latitude = position.coords.latitude,
                longitude = position.coords.longitude
              ),
              (error) => reject(error),
              options
            );

            // const stopWatching = () => {
            //   navigator.geolocation.clearWatch(watchId);
            // };

          } else {
            reject(new Error("Geolocation is not supported by this browser"));
          }
        });
      } else {
        return latitude = 0, longitude = 0;
      }
    }

    function subtractTime(time1, time2) {
      console.log(time1, time2)
      const [hours1, minutes1] = time1.split(':').map(Number);
      const [hours2, minutes2] = time2.split(':').map(Number);
      const totalMinutes1 = hours1 * 60 + minutes1;
      const totalMinutes2 = hours2 * 60 + minutes2;
      const diffMinutes = (totalMinutes1 - totalMinutes2 + 1440) % 1440;
      const resultHours = Math.floor(diffMinutes / 60);
      const remainderMinutes = diffMinutes % 60;
      return `${String(resultHours).padStart(2, '0')}:${String(remainderMinutes).padStart(2, '0')}`;
    }

    function updateLiveTime() {
      timeTextElement.textContent = timeText = formatDateTime(new Date());
    }

    async function fetchJson(url) {
      const response = await fetch(url);
      if (!response.ok) throw new Error('Network response was not ok');
      return response.json();
    }

  
    // Function to populate the list of attendance data in the modal
    function populateAttendanceList(attendanceData) {
      const attendanceListContainer = document.getElementById('attendanceListContainer');
      attendanceListContainer.innerHTML = '';

      const selectElement = document.createElement('select');
      selectElement.className = 'form-control';

      const initialOption = document.createElement('option');
      initialOption.value = '';
      initialOption.textContent = 'Pilih Shift Kehadiran';
      initialOption.style.display = 'none';
      selectElement.appendChild(initialOption);

      attendanceData.forEach((attendance) => {
        const optionElement = document.createElement('option');
        optionElement.value = attendance.attendance_id;
        optionElement.textContent = attendance.attendance_description;
        selectElement.appendChild(optionElement);
      });

      attendanceListContainer.appendChild(selectElement);
    }
    
    if (document.body.innerText.includes('Absensi')) {
      document.title = 'Absensi';
    }
  })
</script>