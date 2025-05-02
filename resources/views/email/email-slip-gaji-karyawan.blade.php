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
					style="font-size: 12px; font-family: 'Cabin', Arial, 'Helvetica Neue', Helvetica, sans-serif; mso-line-height-alt: 18px; color: #566a7f; line-height: 1.5;">
					<p
						style="margin: 0; text-align: left; mso-line-height-alt: 18px;">
						<span style="color:#566a7f;">
							<span
								style="font-size:16px;">Halo
								{{$entity->user_name}},
								<br>Informasi slip gaji periode <b>{{$entity->period}}</b> dari {{$entity->name}} sudah terbit.
								<br>
								<br>Silahkan login menggunakan akun anda untuk melihat slip gaji anda di aplikasi REKKAA.
								<br>
								<br>
								<a
									href="{{url('/karyawan/login')}}"
									class="btn-link"
									style="border: 1px solid #ddd;
									padding: 10px 25px;
									background-color: #ff8139;
									border-radius: 7px;
									text-decoration: auto;
									color: #fff!important;
									display: table;
									margin: auto;
									text-align: center;">Masuk Akun</a>
								<br>
                                <!-- Abaikan email ini jika anda tidak melakukan permintaan tersebut. -->
							</span>
						</span>
					</p>
				</div>
			</div>
		</td>
	</tr>
	@include('email.includes.services')
</table>
@include('email.includes.footer')