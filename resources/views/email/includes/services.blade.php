<tr>
    <td
        class="pad"
        style="padding-left: 15px; padding-right: 15px; padding-top: 45px;"
    >
        <div style="font-family: Arial, sans-serif" id="bx-services">
            @php
            $services = json_decode(getSetting('REKKAA_SERVICES')->setting_value);
            $i = 0;
            @endphp
            @foreach($services as $svc)
            <div class="bx-services-item-child" style="font-size: 12px;
                font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif;
                mso-line-height-alt: 18px;
                color: #566a7f;
                line-height: 1.5;
                width: 30%;
                float: left;">
            
                <div class="bx-services-item" style="text-align: center;">
                    <h3 style="
                    margin: auto;
                    font-size: 1.5em;
                    text-align: center;
                    display: inline-block;
                    position: relative;
                    
                    background-image: url('<?php echo url('assets/img/logo/rekkaa-black.png') ?>');
                    background-position: 0px;
                    background-size: 24px;
                    background-repeat: no-repeat;
                    display: inline-block;
                    padding: 0 23px;
                    color: <?php echo $svc->color ?>;">{{$svc->title}}</h3>
                </div>
                <p
                    style="
                        margin: 0;
                        text-align: center;
                        mso-line-height-alt: 18px;
                    "
                >
                    <span style="color: #566a7f"
                        ><span style="font-size: 13px">
                        {{$svc->description}}
                        <br><a href="{{$svc->link}}">[read more]</a>
                        </span></span
                    >
                </p>
            </div>
            @php 
            $i++;
            @endphp
            @endforeach
        </div>
    </td>
</tr>