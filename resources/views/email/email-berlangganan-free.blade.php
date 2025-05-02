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
                        @php
                            $parse_expired_at = \Carbon\Carbon::parse($subscription->expired_at);
                        @endphp
                        <span style="color: #566a7f"
                            ><span style="font-size: 16px">
                            Selamat bergabung di REKKAA!
                            <br>
                            <br>Terima kasih telah mendaftar di REKKAA, kini <b>{{$subscription->wajibpajak_name}}</b> berlangganan paket <b>{{$subscription->title}}</b>.
                            <br>
                            <br>
                            <!-- <br>Untuk detail fitur lainnya bisa dilihat di halaman berikut https://rekkaa.com/harga. -->
                            Simak terus perkembangan aplikasi kami untuk fitur-fitur yang lebih menarik!
                            </span></span>
                    </p>
                </div>
            </div>
        </td>
    </tr>
    @include('email.includes.services')
</table>
@include('email.includes.footer')