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
                            Selamat {{$content->time}} {{$content->manager_name}},
                            <br>
                            <br>Hari ini {{$content->staff_name}} melakukan Perubahan pada data Pengajuan Cuti menjadi tanggal {{$content->start_date}} selama {{$content->quota_use}} hari.
                            <br>
                            <br>Untuk detail Perubahan yang diajukan, dapat di cek dalam fitur Daftar Cuti.
                            <br>
                            <br>Terima Kasih.
                            </span></span>
                    </p>
                </div>
            </div>
        </td>
    </tr>
</table>
@include('email.includes.footer')