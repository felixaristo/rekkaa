<head>
  <style>
    #start-date,
    #end-date {
      width: 100%;
      /* Adjust the width as needed */
    }
  </style>
</head>
<div class="accordion" id="accordionExample">
  <div class="card accordion-item active">
    <h2 class="accordion-header" id="headingFilter">
      <button type="button" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse" role="tabpanel">
        Filter Lanjutan
      </button>
    </h2>

    <div id="filterCollapse" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <!-- Start Date and End Date inputs here -->
        <div class="row align-items-center">
          <div class="col-md-3">
            <label for="start-date">Tanggal Aktifitas</label>
            <input type="text" id="start-date" name="start-date" class="form-control" autocomplete="off">
          </div>
          <div class="col-md-3 mt-md-4 mt-sm-3 text-md-start text-sm-end">
            <button id="searchButton" class="btn btn-search btn-outline-warning me-2 btn-sm">
              <i class="bx bx-search-alt"></i> Cari
            </button>
            <button id="resetButton" class="btn btn-reset btn-outline-secondary btn-sm" style="background-color: white; color: red; border-color: red;">
              <i class="bx bx-reset"></i> Reset
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-12 mb-3 order-0">
  </div>
</div>

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="card-header m-2">
          <div class="row">
            <div class="col-sm-6">
              <h5>Log Aktifitas</h5>
            </div>
          </div>
        </div>
      <div class="row">
        <div class="col-sm-12">
          <div class="card-body">
            <table class="table table-hover display nowrap" style="width: 100%" id="table-log-aktifitas">
              <thead class="table-light">
                <tr>
                    <th>Tanggal</th>
                    <th>Deskripsi</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    const currentDate = new Date();

    // Calculate the first day of the current month
    const firstDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);

    // Calculate the last day of the current month
    const lastDayOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);

    // Convert the calculated dates to the 'DD-MM-YYYY' format
    const formattedFirstDay = `${('0' + firstDayOfMonth.getDate()).slice(-2)}-${('0' + (firstDayOfMonth.getMonth() + 1)).slice(-2)}-${firstDayOfMonth.getFullYear()}`;
    const formattedLastDay = `${('0' + lastDayOfMonth.getDate()).slice(-2)}-${('0' + (lastDayOfMonth.getMonth() + 1)).slice(-2)}-${lastDayOfMonth.getFullYear()}`;

    $(`#start-date`).daterangepicker({
      singleDatePicker: false,
      showDropdowns: true,
      locale: {
        format: 'DD-MM-YYYY'
      },
      startDate: formattedFirstDay, // Set the calculated first day as the start date
      endDate: formattedLastDay,
    });

    $("#resetButton").on("click", function() {
      $("#start-date").val(`${formattedFirstDay} - ${formattedLastDay}`);
      $('#start-date').data('daterangepicker').setStartDate(formattedFirstDay);
      $('#start-date').data('daterangepicker').setEndDate(formattedLastDay);
      tblPengaturanPengumuman.draw();
    });

    $("#searchButton").on("click", function() {
      tblPengaturanPengumuman.draw();
    });

    let tblPengaturanPengumuman = $("#table-log-aktifitas").DataTable({
      // DataTable configuration options
      "searching": false,
      "searchDelay": 1050,
      "processing": true,
      "serverSide": true,
      "language": {
        // "emptyTable": "Tidak ada data"
        // "searchPlaceholder": "Cari Judul Pengumuman",
      },
      "info" : true,
      "ordering" : true,
      "ajax": {
        "url": `{{ route('user.page.log-aktifitas.datatable') }}`, // Replace with your actual route to fetch data
        "type": "GET",
        "data": function(data) {
          const dates = $('#start-date').val().split(' - ');
          data.start_date = dates[0]
          data.end_date = dates[1]
        }
      },
      "fnInitComplete": function() {
        //  this.fnAdjustColumnSizing(true);
      },
      "autoWidth": true,  
      "columns": [
        { 
          "data": "created_at", 
          "title": "Tanggal", 
          "className": "text-center" 
        },
        {
          "data": "description",
          "title": "Deskripsi",
          "className": "text-center"
        },
      ],
      "columnDefs": [
        // Define which columns to hide
      ],
    });

    setHtmlTitle('{{$title}}')
  });
</script>
