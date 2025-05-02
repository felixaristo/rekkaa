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
                                Halo <b>{{ $subscription->wajibpajak_name }}</b>,
                                <br>
                                <br />Masa berlangganan Anda telah habis, kini <b>{{$subscription->wajibpajak_name}}</b> kembali berlangganan paket <b>Gratis</b>.
                                <br>
                                <br />Segera untuk melakukan pembayaran dengan cara klik tautan di bawah ini untuk dapat kembali menikmati fitur dan layanan premium dari REKKAA.
                                <br>
                                <br>
                                <a href="{{url('/user/profil')}}" class="btn-link"
                                style="border: 1px solid #ddd;
									padding: 10px 25px;
									background-color: #ff8139;
									border-radius: 7px;
									text-decoration: auto;
									color: #fff!important;
									display: table;
									margin: auto;
									text-align: center;">Perpanjang Layanan</a>
                            </span></span
                        >
                    </p>
                </div>
            </div>
        </td>
    </tr>
    @include('email.includes.services')
</table>
@include('email.includes.footer')
