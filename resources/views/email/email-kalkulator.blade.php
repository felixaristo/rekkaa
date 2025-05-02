@include('email.includes.header')
	<table border="0" cellpadding="0" cellspacing="0"
		class="text_block block-4 email-text-content" role="presentation"
		style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;"
		width="100%">
		<tr>
			<td class="pad"
				style="padding-left:45px;padding-right:45px;padding-top:10px;">
				<div style="font-family: Arial, sans-serif">
					<div class=""
						style="font-size: 12px; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; mso-line-height-alt: 18px; color: #393d47; line-height: 1.5;">
						<p
							style="margin: 0; text-align: center; mso-line-height-alt: 18px;">
							<span style="color:#566a7f;"><span
									style="font-size:18px;">Terimakasih
									telah menggunakan layanan kami.
								</span></span></p>
						<p
							style="margin: 0; text-align: center; mso-line-height-alt: 27px;">
							<span style="color:#566a7f;"><span
									style="font-size:18px;">Berikut
									terlampir dokumen perhitungan
									{{$tipe}}.</span></span></p>
					</div>
				</div>
			</td>
		</tr>
	                               
	@include('email.includes.services')
</table>
@include('email.includes.footer')