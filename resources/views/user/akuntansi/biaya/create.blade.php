<head>
    <style>
        #table-history th,
        #table-history td,
        #table-history .dataTables_paginate {
            font-size: 12px;
            /* Adjust the font size as desired */
        }
    </style>
</head>
<div class="row">
    <div class="col-lg-12 mb-4 order-0">
        <!-- Bootstrap Table with Header - Light -->
        <div class="card">
            <div class="card-header row">
                <div class="col-sm-6">
                    <h5 class="mb-0">{{$title}}</h5>
                </div>
            </div>
            <div class="card-body">
                <form action="{{route('user.page.akuntansi.biaya.create')}}?menu_id={{request()->get('menu_id')}}" method="POST" class="form-horizontal form-lbl-dot" id="formAKBiaya" autocomplete="off">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group row mb-3">
                                <label for="biaya_nomor_bukti" class="col-sm-2 lbl-req">Nomor Bukti</label>
                                <div class="col-sm-4">
                                    <input placeholder="Nomor Bukti" name="biaya_nomor_bukti" required id="biaya_nomor_bukti" class="form-control" />
                                </div>
                                <label for="biaya_cara_pembayaran" class="col-sm-2 lbl-req">Cara Pembayaran</label>
                                <div class="col-sm-4">
                                    <select data-placeholder="Pilih Cara Pembayaran" name="biaya_cara_pembayaran" required id="biaya_cara_pembayaran" class="form-control" style="width: 100%;">
                                        <option value=""></option>
                                        <option value="CASH">CASH</option>
                                        <option value="DEBET">DEBET</option>
                                        <option value="KREDIT">KREDIT</option>
                                        <option value="TRANSFER">TRANSFER</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="biaya_tgl" class="col-sm-2 lbl-req">Tgl. Transaksi</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Tgl. Transaksi" name="biaya_tgl" required id="biaya_tgl" class="form-control" />
                                </div>
                                <label for="biaya_dari" class="col-sm-2 lbl-req">Bayar Dari</label>
                                <div class="col-sm-4">
                                    <select data-placeholder="Pilih Bayar Dari" name="biaya_dari" required id="biaya_dari" class="form-control" style="width: 100%;"></select>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="biaya_no" class="col-sm-2">Nomor Biaya</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Nomor Biaya" readonly name="biaya_no" id="biaya_no" class="form-control" />
                                </div>
                                <label for="biaya_total" class="col-sm-2">Total Biaya</label>
                                <div class="col-sm-4">
                                    <input type="text" placeholder="Total Biaya" readonly name="biaya_total" value="0" id="biaya_total" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <label for="penerima" class="col-sm-2 lbl-req">Penerima</label>
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <!-- style="width: 100%;" -->
                                        <select data-placeholder="Pilih Penerima" name="biaya_penerima" required id="biaya_penerima" class="form-control" style="width:85%"></select>
                                        <button type="button" class="btn btn-info btn-sm" id="tambah_penerima_btn"><span class="bx bx-plus"></span></button>
                                    </div>
                                </div>
                                <label for="arus_kas" class="col-sm-2 lbl-req">Jenis Arus Kas</label>
                                <div class="col-sm-4">
                                    <select data-placeholder="Pilih Jenis Arus Kas" name="biaya_arus_kas" required id="biaya_arus_kas" class="form-control" style="width:100%"></select>
                                </div>
                            </div>
                            <div class="form-group row mb-3">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <label for="biaya_nomor_bukti_file" class="col-sm-4">Foto Bukti</label>
                                        <div class="col-sm-8">
                                            <input name="biaya_nomor_bukti_file" class="form-control" type="file" id="biaya_nomor_bukti_file">
                                            <span class="help-block text-danger" style="font-style: italic;font-size: 12px;">Ukuran Maksimal 250Kb, Format: jpg / png</span>
                                            <!-- <div class="file-box mt-2 mb-2"></div> -->
                                        </div>
                                    </div>
                                </div>
                                <label for="biaya_keterangan" class="col-sm-2 lbl-req">Keterangan</label>
                                <div class="col-sm-4">
                                    <textarea rows="3" placeholder="Keterangan" name="biaya_keterangan" required id="biaya_keterangan" class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row mb-3">
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-hover" style="width: 100%" id="biaya_item_datatable">
                                <thead>
                                    <tr>
                                        <th class="text-center">Akun Biaya</th>
                                        <th class="text-center">Deskripsi</th>
                                        <th class="text-center">Nominal</th>
                                        <th class="text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <hr>
                    </div>
                    <div class="form-group row mb-3">
                        <div class="col-sm-12 text-right">
                            <button type="reset" class="btn btn-sm btn-danger mr-2"><i class="fa fa-eraser"></i> Reset</button>
                            <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-save"></i> Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Bootstrap Table with Header - Light -->
    </div>
</div>


<script src="{{asset('assets/js/reload.js')}}"></script>

<script>
    $(function() {
        let currentMenuId = "{{request()->get('menu_id')}}";
        let [numTotal] = AutoNumeric.multiple([
            "#formAKBiaya [name=biaya_total]"
        ], {decimalCharacter: ',', digitGroupSeparator: '.', unformatOnSubmit: true, minimumValue: 0});

        $("#biaya_cara_pembayaran, #biaya_penerima").select2();
        $("#biaya_dari").select2({
            ajax: {
				url: `{{route('user.page.master.akun.select')}}?menu_id=${currentMenuId}`,
				data: function(params) {
					var query = {
                        akun_level: 5,
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function(data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.akun_id;
						item.text = item.akun_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
        })
        $("#biaya_arus_kas").select2({
            ajax: {
				url: `{{route('user.page.master.akun.aruskas.select')}}?menu_id=${currentMenuId}`,
				data: function(params) {
					var query = {
						q: params.term,
						type: 'public'
					}

					// Query parameters will be ?search=[term]&type=public
					return query;
				},
				processResults: function(data) {
					// Transforms the top-level key of the response object from 'items' to 'results'
					// console.log('data.data', data.data)
					let items = data.data;
					items.map((item, idx) => {
						item.id = item.akunaruskas_id;
						item.text = item.akunaruskas_name;
						// console.log('item.kode', item)
						return item
					})
					return {
						results: items
					};
				},
			},
        })
        let tblBItem = $("#biaya_item_datatable").DataTable({
            "pageLength": 1000,
            "order": [], //Initial no order.
            "sDom": "<'vitem-btn-left-action'>t<'vitem-btn-bottom-action'>",
            "autoWidth": true,
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 3]
            },{
                "targets": [3],
                "className": "text-center",
                "width": 90
            }],
            "columns": [{
                    "data": "akun_biaya",
                    "render": function(data, type, row) {
                        let option = '';
                        if(row.akun_biaya) {
                            option += '<option selected value="'+row.akun_biaya+'">'+row.akun_nama+'</option>'
                        }
                        return '<select class="form-control select_akun5" name="akun_id[]" required style="width:100%" data-placeholder="Pilih Akun">'+option+'</select>'
                    }
                },
                {
                    "data": "akun_deskripsi",
                    "render": function(data, type, row) {
                        return '<input type="text" required name="akun_deskripsi[]" class="form-control akun_deskripsi" value="'+data+'" />'
                    }
                },
                {
                    "data": "akun_nominal",
                    "render": function(data, type, row) {
                        return '<input type="text" required name="akun_nominal[]" class="form-control text-right ianominal input_akun_nominal'+row.inc_numerik+'" data-numerik=".input_akun_nominal'+row.inc_numerik+'" text-right" value="'+data+'" />'
                    }
                },
                {
                    "data": "akun_action",
                    "render": function(data, type, row) {
                        return '<button type="button" class="btn-delete-item-biaya btn btn-sm btn-danger"><i class="bx bx-trash"></i></button>'
                    }
                }
            ],
        });

        $('#formAKBiaya .vitem-btn-left-action').append('<button type="button" class="btn btn-sm btn-info btn-tambah-item"><i class="fa fa-plus"></i> Tambah Biaya</button>')

        let inc_numerik = 0;
        $('#formAKBiaya .btn-tambah-item').click(function(e) {
            e.preventDefault();
            tblBItem.row.add({'akun_biaya': null, 'akun_deskripsi': '', 'akun_nominal': 0, 'akun_action': null, 'inc_numerik': inc_numerik, 'biayadet_id': null, 'akun_nama': null});

            tblBItem.draw();
            selectAkun();
            if (AutoNumeric.getAutoNumericElement('#biaya_item_datatable tbody .input_akun_nominal'+inc_numerik) === null) {
                new AutoNumeric("#biaya_item_datatable tbody .input_akun_nominal"+inc_numerik, {decimalCharacter: ',', digitGroupSeparator: '.', unformatOnSubmit: true, minimumValue: 0});
            }
            // console.log('inc_numerik before', inc_numerik)
            inc_numerik++;
            // console.log('inc_numerik after', inc_numerik)
        })

        $("#biaya_item_datatable tbody").on("keyup", ".ianominal", function(e) {
            e.preventDefault();
            let tr = $(this).closest('tr');
            let indexBItem = tblBItem.row(tr).index();
            let dtNumNominal = $(this).attr('data-numerik');
            // let nominal = AutoNumeric.getAutoNumericElement(dtNumNominal).getNumber();
            // let dtNumTotalBiaya = tblBItem.row(indexBItem).nodes().to$().find('.iadebet').attr('data-numerik');
            // AutoNumeric.getAutoNumericElement(dtNumTotalBiaya).set(0);

            updateTotalNominal();
        })

        $("#biaya_item_datatable tbody").on('click', '.btn-delete-item-biaya', function(e) {
            e.preventDefault();
            let tr = $(this).closest('tr');
            let currentRowData = tblBItem.row(tr).data();
            console.log('currentRowData', currentRowData);
            let akun_id = currentRowData.akun_id;
            let akun_deskripsi = currentRowData.akun_deskripsi;
            let akun_nominal = currentRowData.akun_nominal;
            
            Swal.fire({
                html: 'Apakah anda yakin menghapus akun ini ini?',
                // html: "<b>Nama Komponen</b><br>" + currentRowData.komponentarif_nama,
                icon: 'question',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.value) {
                    tblBItem.row(tr).remove().draw();
                }
            })
        })

        function selectAkun() {
            $("#biaya_item_datatable tbody .select_akun5").select2({
                ajax: {
                    url: `{{route('user.page.master.akun.select')}}?menu_id=${currentMenuId}`,
                    delay: 250,
                    dataType: 'json',
                    data: function (params) {
                        let query = {
                            akun_level: 5,
                            akun_kode: 5,
                            q: params.term,
                        }

                        // Query parameters will be ?search=[term]&type=public
                        return query;
                    },
                    processResults: function(data) {
                        // Transforms the top-level key of the response object from 'items' to 'results'
                        let items = data.data;
                        items.map((item, idx) => {
                            item.id = item.akun_id;
                            item.text = item.akun_name;
                            // console.log('item.kode', item)
                            return item
                        })
                        return {
                            results: items
                        };
                    }
                }
            }).on("select2:select", function(e) {
                let data = e.params.data;
                console.log(data);
            });
        }

        function updateTotalNominal() {
            let totalNominal = 0;
            for(let i=0; i<tblBItem.rows().data().length; i++) {
                // console.log('tblBItem.rows().data()', tblBItem.row(i).data())
                let dtNumNominal = tblBItem.row(i).nodes().to$().find('.ianominal').attr('data-numerik');
                let nominal = AutoNumeric.getAutoNumericElement(dtNumNominal).getNumber();
                totalNominal += nominal;
            }

            numTotal.set(totalNominal)
        }

        // set meta title
        setHtmlTitle('{{$title}}')
    })
</script>