<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <!-- Bootstrap Table with Header - Light -->
    <div class="card">
      <div class="card-header row">
        <div class="col-sm-6">
          <h5 class="mb-0">{{$title}}</h5>
        </div>
      </div>
      <div class="card-body row">
        <div class="col-sm-12">
          <div class="nav-align-top mb-4">
            <ul class="nav nav-tabs mb-3 nav-fill" role="tablist">
              <li class="nav-item" role="presentation">
                <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-pph21" aria-controls="navs-tabs-justified-pph21" tabindex="-1"><i class="bx bx-file"></i> PPh 21</button>
              </li>
              <!-- <li class="nav-item" role="presentation">
                <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-tabs-justified-ppn" aria-controls="navs-tabs-justified-ppn" tabindex="-1"><i class="bx bx-file"></i> PPN</button>
              </li> -->
            </ul>
            <div class="tab-content">
              <div class="tab-pane fade active show" id="navs-tabs-justified-pph21" role="tabpanel">
                <div class="row">
                  <div class="col-lg-12">
                    @include('user.pengaturan.pajak.pph21')
                  </div>
                </div>
              </div>
              <!-- <div class="tab-pane fade" id="navs-tabs-justified-ppn" role="tabpanel">
                <div class="row">
                  <div class="col-lg-12">
                    ppn
                  </div>
                </div>
              </div> -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Bootstrap Table with Header - Light -->
  </div>
</div>

<script src="{{asset('assets/js/reload.js')}}"></script>
<script>
  $(function() {
    let currentMenuId = "{{request()->get('menu_id')}}";


    // set meta title
    setHtmlTitle('{{$title}}')
  })
</script>