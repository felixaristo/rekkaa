<?php 
$doaction = route('doregisterconfirmation');
if(isset($wajibpajak) && $wajibpajak) {
    $doaction = route('user.page.subscription.doextendconfirmation');
    if(request()->segment(3) == 'upgrade') {
        $doaction = route('user.page.subscription.doupgradeconfirmation');
    }
}
?>
<form id="form-3" class="mb-3 form-lbl-dot row" action="{{$doaction}}" method="POST" autocomplete="off"></form>
    <ul class="list-group list-group-horizontal-md">
        <li class="list-group-item flex-fill p-4 text-heading">
            <h6 class="d-flex align-items-center gap-1"><i class="bx bx-map"></i> Informasi Entitas</h6>
            <hr>
            <address class="mb-0 summary-entity">
            </address>
        </li>
        <li class="list-group-item flex-fill p-4 text-heading">
            <h6 class="d-flex align-items-center gap-1"><i class="bx bx-user"></i> Info Akun</h6>
            <hr>
            <address class="mb-0 summary-account">
            </address>
        </li>
    </ul>
    @if($subscription->subscription_type != 'FREESSS')
    <ul class="list-group list-group-horizontal-md mt-3">
        <li class="list-group-item flex-fill p-4 text-heading">
            <h6 class="d-flex align-items-center gap-1"><i class="bx bx-tag"></i> Add On</h6>
            <hr>
            <div class="row">
                <ul class="list-group" id="list-group-addon-summary">
            
		        </ul>
            </div>
        </li>
    </ul>
    @endif
<script>
    $(function() {
    })
</script>