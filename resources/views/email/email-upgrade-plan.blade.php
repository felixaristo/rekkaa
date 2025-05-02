@include('email.includes.header')
<table
    border="0"
    cellpadding="0"
    cellspacing="0"
    class="text_block block-4 email-text-content"
    role="presentation"
    style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word"
    width="100%"
>
    <tr>
        <td
            class="pad"
            style="padding-left: 15px; padding-right: 15px; padding-top: 10px"
        >
            <div style="font-family: Arial, sans-serif">
                <div
                    class=""
                    style="
                        font-size: 12px;
                        font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica,
                            sans-serif;
                        mso-line-height-alt: 18px;
                        color: #566a7f;
                        line-height: 1.5;
                    "
                >
                    <p
                        style="
                            margin: 0;
                            text-align: left;
                            mso-line-height-alt: 18px;
                        "
                    >
                        <span style="color: #566a7f"
                            ><span style="font-size: 16px">
                            @php
                            $parse_expired_at = \Carbon\Carbon::parse($subscription->expired_at);
                            @endphp
                            Halo <b>{{ $user->user_name }}</b>,
                            <br>
                            <br>Selamat, sekarang <b>{{$subscription->wajibpajak_name}}</b> berlangganan di <b>{{$subscription->title}}</b> sampai dengan tanggal <b>{{$parse_expired_at->format('d-m-Y')}}</b>. Nikmati pengalaman yang lebih baik menggunakan fitur premium dari REKKAA.
                            </span></span>
                    </p>
                </div>
            </div>
        </td>
    </tr>                        
    @include('email.includes.services')
</table>
@include('email.includes.footer')