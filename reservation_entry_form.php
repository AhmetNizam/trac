<?php
	require("./library.php");

	unset($_SESSION['reservation']);

	$requestid = $_GET['requestid'] ?? '0';
?>
<!doctype HTML 4.01 Transitional>
<html xmlns="http://www.w3.org/2001/XMLSchema">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=750">

		<title>Ulaşım ve Konaklama - Rezervasyon</title>

		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.13.3.css" />
		<link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

		<style>
			.ui-autocomplete {
				max-height: 323px;
				overflow-y: auto;
				/* prevent horizontal scrollbar */
				overflow-x: hidden;
				font-family: 'Century Gothic';
				font-size: 12px;
				line-height: 10px;
				outline-width: thick;
				/* background-color: aquamarine; */
			}
			/* IE 6 doesn't support max-height
			* we use height instead, but this forces the menu to always be this tall
			*/
			* html .ui-autocomplete {
				height: 320px;
			}
		</style>

		<script src="./js/jquery-3.7.1.js"></script>
		<script src="./js/jquery-ui-1.13.3.js"></script>

		<script src="./js/jquery.inputmask.min.js"></script>
		<script src="./js/inputmask.binding.js"></script>

		<script type="text/javascript">

			var cssFile = './css/main.css';
			var cssDoc = '';
			var xmlFile = './xml/alert.xml';
			var xmlDoc = '';

			$.ajax({
				type: "GET",
				url: cssFile,
				dataType: "text",
				success: function(cssText) {
					cssDoc = cssText;
				},
				error: function(xhr, status, error) {
					alert("css dosyası okunamadı: " + error);
				}
			});

			$.ajax({
				type: "GET",
				url: xmlFile,
				dataType: "xml",
				success: function(xmlText) {
					xmlDoc = $(xmlText);
				},
				error: function(xhr, status, error) {
					alert("XML dosyası okunamadı: " + error);
				}
			});

			var available_tags = {
				departure: [],
				return: []
			};

			$(document).ready(function() {

				var now = new Date();
				var year = now.getFullYear();
				var month = ('0' + (now.getMonth() + 1)).slice(-2); // Aylar 0-11 arası olduğundan +1 eklenir
				var day = ('0' + now.getDate()).slice(-2);
				var hours = ('0' + now.getHours()).slice(-2);
				var minutes = ('0' + now.getMinutes()).slice(-2);

				// Yerel saati ISO formatına uygun şekilde birleştirme
				var today = `${year}-${month}-${day}T${hours}:${minutes}`;
				var nextyear = `${year + 1}-${month}-${day}T${hours}:${minutes}`;

				$('input[type="datetime-local"]').attr('min', today);
				$('input[type="datetime-local"]').attr('max', nextyear);

				$('input[type="datetime-local"]').on('change', function(event) {
					if($(event.target).val().length >= 16) {
						let invalid_date = 0;
						let item = $(event.target);
						let now = new Date();
						let date = new Date(item.val());

						if(date < now) {
							alert(get_label(item) + ' Bugünden önce olamaz.');
							invalid_date = 1;
						} else if(item.attr('date-type') == 'start_date') {
							let end_item = $('input[date-group="' + item.attr('date-group') + '"][date-type="end_date"]');
							let end_date = new Date(end_item.val());

							if(date > end_date) {
								alert(get_label(item) + ' ' + get_label(end_item) + 'nden sonra olamaz.');
								invalid_date = 1;
							}
						} else if(item.attr('date-type') == 'end_date') {
							let start_item = $('input[date-group="' + item.attr('date-group') + '"][date-type="start_date"]');
							let start_date = new Date(start_item.val());

							if(date < start_date) {
								alert(get_label(item) + ' ' + get_label(start_item) + 'nden önce olamaz.');
								invalid_date = 1;
							}
						}

						if(!invalid_date && item.attr('date-group') == '2') {
							let start_item = $('input[date-group="1"][date-type="start_date"]');
							let start_date = new Date(start_item.val());
							let end_item = $('input[date-group="1"][date-type="end_date"]');
							let end_date = new Date(end_item.val());

							if(date < start_date) {
								alert(get_label(item) + ' ' + get_label(start_item) + 'nden önce olamaz.');
								invalid_date = 1;
							} else if(date > end_date) {
								alert(get_label(item) + ' ' + get_label(end_item) + 'nden sonra olamaz.');
								invalid_date = 1;
							}
						}

						if(invalid_date) {
							item.val('');
							item.blur();
							item.focus();
						}

						if(item.attr('date-group') == '1') {
							$('input[date-group="2"][date-type="' + item.attr('date-type') + '"]').val(item.val());
						}
					}
				});

				$(window).on('resize', function(event) {
					$('#div_main_area').css('margin-left', ($(window).width() - $('#div_main_area').width()) / 2);
				});

				$('select').on('change', function(event) {
					var item = $(event.target).attr('id').replace('sel', '');
					$('#inp' + item).val($('#sel' + item + ' option:selected').text());
					set_transportation_mode($(this));
				});

				$('#inp_departure_company').on('focus', function(event) {
					$(this).autocomplete('search', '');
				});

				$('#inp_return_company').on('focus', function(event) {
					$(this).autocomplete('search', '');
				});

				$('#inp_departure_company').autocomplete({
					source: available_tags['departure'],
					minLength: 0,
					autoFocus: false
				});

				$('#inp_return_company').autocomplete({
					source: available_tags['return'],
					minLength: 0,
					autoFocus: false
				});

				fill_selection_list($('#sel_departure_transportation_mode'), 'transportation_mode');
				fill_selection_list($('#sel_return_transportation_mode'), 'transportation_mode');
			});

			function get_label(item) {
				let label = $('#' + item.attr('label')).html();
				label = label.substring(0, label.indexOf(':'));
				return label;
			}

			function toggle_visibility(visible_items, unvisible_items) {
				$.each(unvisible_items, function() { $(this).hide(); });
				$.each(visible_items, function() { $(this).show(); });
			}

			function reset_alerts() {
				$('[class=lbl_alrt1]').attr('class', 'lbl_norm1');
				$('[class=lbl_alrt2]').attr('class', 'lbl_norm2');
			}

			function get_list(list_table, field_name = '', where = '') {
				return new Promise((resolve, reject) => {
					$.getJSON('./get_selection_list.php?table_name=' + list_table + '&field_name=' + field_name + '&where=' + where,
						function(data) {
							resolve(data);
						}
					)
					.fail(
						function(error) {
							reject(error);
						}
					)
				});
			}

			function fill_selection_list(sel_item, list_table, field_name = '', where = '') {
				get_list(list_table, field_name, where).then(data => {
					if(data.Rows) {
						var content = '<option value="">-- Seçiniz --</option>';
						if(sel_item.attr('id') == 'sel_from_location') {
							content += '<option value="0">Diğer</option>';
						}
						for(var i = 0; i < data.Rows.length; i++) {
							var rowData = data.Rows[i];
							content += '<option value="' + rowData.id + '">' + rowData.name + '</option>';
						}
						sel_item.html(content);
					}
				});
			}

			function visibility(item) {
				if(item.is(':visible')) {
					if(item.parent().prop('tagName') != 'HTML') {
						return visibility(item.parent());
					} else {
						return true;
					}
				} else {
					return false;
				}
			}

			function check_form(item) {
				reset_alerts();

				var result = true;
				var area = item.attr('area');
				var action = item.attr('action');
				var xmlNodes = $.merge(xmlDoc.find('input[id="' + item.attr('id') + '"]'), xmlDoc.find(area).children());

				xmlNodes.each(
					function() {
						if(visibility($('#' + $(this).attr('id')))) {
							if(eval($(this).children('condition').text())) {
								var label_item = $('#' + $('#' + $(this).attr('id')).attr('label'));
								var label_text = label_item.html().substring(0, label_item.html().indexOf(':'));
								if(action == 'warn') {
									alert($(this).children('alert').text().replace('""', '"' + label_text + '"'));
									label_item.attr('class', label_item.attr('alert'));
								} else if($(this).children('forced_alert').text() == '1') {
									alert(label_text + $(this).children('alert').text().replace('""', '"' + label_text + '"'));
								}
								if($(this).attr('id') != item.attr('id')) {
									$('#' + $(this).attr('id')).focus();
								}
								result = false;
								return result;
							}
						}
					}
				);
				if(result) {
					$('#' + $(xmlDoc.find(area)).attr('focus')).focus();
				}
				return result;
			}

			function set_transportation_mode(item) {
				const direction = item.attr('id').substring(4, item.attr('id').indexOf('_', 4));

				$('[id*="div_' + direction + '_"]').hide();

				if(item.val() == '1') {
					toggle_visibility([$('#div_' + direction + '_port'), $('#div_' + direction + '_date')], []);
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_' + direction + '_port'), $('#div_' + direction + '_date')], []);
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_' + direction + '_port'), $('#div_' + direction + '_company'), $('#div_' + direction + '_pnr_code'),
									   $('#div_' + direction + '_ticket_number'), $('#div_' + direction + '_ticket_price'), $('#div_' + direction + '_date')], []);
				} else if(item.val() == '4') {
					toggle_visibility([$('#div_' + direction + '_port'), $('#div_' + direction + '_company'), $('#div_' + direction + '_date')], []);
				} else if(item.val() == '5') {
					toggle_visibility([$('#div_' + direction + '_port'), $('#div_' + direction + '_car_license_plate'), $('#div_' + direction + '_date')], []);
				}

				$.getJSON('./get_transportation_company.php?transportation_mode_id=' + item.val(),
					function(data) {
						if(data.Rows) {
							available_tags[direction].splice(0, available_tags[direction].length);
							for(var i = 0; i < data.Rows.length; i++) {
								var rowData = data.Rows[i];
								available_tags[direction].push(rowData.name);
							}
						}
					}
				).done(
					function() {
						console.log(direction + ':', available_tags[direction].length);
					}
				);

				if(direction == 'departure') {
					$('#sel_return_transportation_mode').val($('#sel_departure_transportation_mode').val());
					set_transportation_mode($('#sel_return_transportation_mode'));
				}
			}

			function add_reservation(item) {
				if(check_form(item)) {
					var formData = $('#form1').serialize();

					$.getJSON('./add_reservation.php?' + formData,
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								if(rowData.status == '1') {
									$('#div_form_page').hide();
									$('#div_approve_page').load('./reservation_approval_form.php');
									$('#div_approve_page').show();
								} else {
									alert('Rezervasyon oluşturulurken bir hata oluştu!');
								}
							} else {
								alert('Rezervasyon oluşturulurken bir hata oluştu!');
							}
						}
					);
				} else {
					return false;
				}
			}

			function save_reservation() {
				$.getJSON('./save_reservation.php',
					function(data) {
						if(data.Rows) {
							var rowData = data.Rows[0];
							if(rowData.status == '1') {
								send_mail();
							} else {
								alert('Rezervasyon kaydedilirken bir hata oluştu!');
							}
						} else {
							alert('Rezervasyon kaydedilirken bir hata oluştu!');
						}
					}
				);
			}

			function send_mail() {
				const htmlContent = document.documentElement.outerHTML;
				const doc = new DOMParser().parseFromString(htmlContent, "text/html");

				const link = doc.getElementsByTagName('link');
				for(let i = link.length - 1; i >= 0; i--) {
					link[i].remove();
				}

				const script = doc.getElementsByTagName('script');
				for(let i = script.length - 1; i >= 0; i--) {
					script[i].remove();
				}

				doc.getElementById('div_form_page').remove();
				doc.getElementById('div_save_buttons').remove();
				doc.getElementById('div_completion_page').remove();
				doc.getElementById('div_main_area').style.width = '900px';
				doc.getElementById('div_main_area').style.margin = '0px';
				doc.getElementById('div_preview_page').style.display = 'block';
				doc.getElementById('div_approval_buttons').style.display = 'block';

				const style = doc.createElement('style');
				style.setAttribute('type', 'text/css');
				style.innerHTML = '\n' + cssDoc + '\n';
				doc.getElementsByTagName('head')[0].appendChild(style);

				let htmlDoc = doc.documentElement.outerHTML;
				htmlDoc = htmlDoc.replace('><head>', '>\n\t<head>').replace('<style', '\t<style').replace('</style></head>', '\t\t</style>\n\t</head>').replace('</body></html>', '\t</body>\n</html>');
				const lines = htmlDoc.split('\n');
				var htmlText = '';

				lines.forEach(
					function(line) {
						if(line.trim() != '') {
							htmlText += line + '\n';
						}
					}
				);

				console.clear();
				console.log(htmlText);

				$.ajax({
					type: "POST",
					url: "./send_mail.php",
					data: { mailBody: htmlText },	// Gönderilecek veri
					success: function(response) {
						toggle_visibility([$('#div_completion_page')], [$('#div_preview_page')]);
						console.log('Mail gönderimi başarılı.');
						console.log('Sunucudan gelen yanıt:', response);
					},
					error: function(xhr, status, error) {
						alert('Talep kaydedildi ancak mail gönderilirken bir hata oluştu!');
						console.error('Talep kaydedildi ancak mail gönderilirken bir hata oluştu!', error);
					}
				});
			}

			function cancel_reservation() {
				if(confirm('Rezervasyon iptal edilecek.\nDevam etmek istiyor musunuz?')) {
					window.open('./reservation_entry_form.php', '_self');
				}
			}

		</script>
	</head>
	<body>
		<div id="div_main_area" hidden>
			<div id="div_main_frame" class="main_frame">
				<div id="div_form_page">
					<form id="form1" method="post" enctype="multipart/form-data" action="">
						<input type="hidden" id="inp_requestid" name="inp_requestid" value="<?php echo $requestid; ?>" />
						<div style="height: 40px;"></div>
						<div class="heading">ULAŞIM ve KONAKLAMA REZERVASYON FORMU</div>
						<div id="div_transportation">
							<div style="height: 20px;"></div>
							<div align="left" class="blue_line">Ulaşım Bilgileri</div>
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div align="center" style="width: 49%;">
									<div align="left" class="lbl_norm2">Gidiş</div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div align="left" class="lbl_norm2">Dönüş</div>
								</div>
							</div>
							<div style="display: flex;">
								<div align="center" style="width: 49%;">
									<div style="height: 100%; border: solid 1px red;">
										<div style="height: 12px;"></div>
										<div align="left" style="width: 90%;">
											<div style="height: 58px;">
												<div id="div_lbl1" class="lbl_norm2" alert="lbl_alrt2">Ulaşım Yöntemi: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_departure_transportation_mode" name="inp_departure_transportation_mode" />
													<select id="sel_departure_transportation_mode" name="sel_departure_transportation_mode" label="div_lbl1" area="area3" action="go" class="inp"></select>
												</div>
											</div>
											<div id="div_departure_port" style="height: 58px;" hidden>
												<div id="div_lbl2" class="lbl_norm2" alert="lbl_alrt2">Kalkış Yeri: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_departure_port" name="inp_departure_port" label="div_lbl2" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_company" style="height: 58px;" hidden>
												<div id="div_lbl3" class="lbl_norm2" alert="lbl_alrt2">Seyahat Firması: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_departure_company" name="inp_departure_company" label="div_lbl3" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_pnr_code" style="height: 58px;" hidden>
												<div id="div_lbl4" class="lbl_norm2" alert="lbl_alrt2">PNR Kodu: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_departure_pnr_code" name="inp_departure_pnr_code" label="div_lbl4" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_ticket_number" style="height: 58px;" hidden>
												<div id="div_lbl5" class="lbl_norm2" alert="lbl_alrt2">Bilet Numarası: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_departure_ticket_number" name="inp_departure_ticket_number" label="div_lbl5" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_ticket_price" style="height: 58px;" hidden>
												<div id="div_lbl6" class="lbl_norm2" alert="lbl_alrt2">Fiyat Bilgisi:</div>
												<div><input type="text" id="inp_departure_ticket_price" name="inp_departure_ticket_price" label="div_lbl6" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_car_license_plate" style="height: 58px;" hidden>
												<div id="div_lbl7" class="lbl_norm2" alert="lbl_alrt2">Araç Plakası: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_departure_car_license_plate" name="inp_departure_car_license_plate" label="div_lbl7" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_departure_date" style="height: 58px;" hidden>
												<div id="div_lbl8" class="lbl_norm2" alert="lbl_alrt2">Gidiş Tarihi: <font style="color: red;">*</font></div>
												<div><input type="datetime-local" date-group="1" date-type="start_date" id="inp_departure_date" name="inp_departure_date" label="div_lbl8" area="area3" action="go" class="inp" /></div>
											</div>
										</div>
										<div style="height: 8px;"></div>
									</div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div style="height: 100%; border: solid 1px red;">
										<div style="height: 12px;"></div>
										<div align="left" style="width: 90%;">
											<div style="height: 58px;">
												<div id="div_lbl9" class="lbl_norm2" alert="lbl_alrt2">Ulaşım Yöntemi: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_return_transportation_mode" name="inp_return_transportation_mode" />
													<select id="sel_return_transportation_mode" name="sel_return_transportation_mode" label="div_lbl9" area="area3" action="go" class="inp"></select>
												</div>
											</div>
											<div id="div_return_port" style="height: 58px;" hidden>
												<div id="div_lbl10" class="lbl_norm2" alert="lbl_alrt2">Kalkış Yeri: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_return_port" name="inp_return_port" label="div_lbl10" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_company" style="height: 58px;" hidden>
												<div id="div_lbl11" class="lbl_norm2" alert="lbl_alrt2">Seyahat Firması: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_return_company" name="inp_return_company" label="div_lbl11" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_pnr_code" style="height: 58px;" hidden>
												<div id="div_lbl12" class="lbl_norm2" alert="lbl_alrt2">PNR Kodu: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_return_pnr_code" name="inp_return_pnr_code" label="div_lbl12" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_ticket_number" style="height: 58px;" hidden>
												<div id="div_lbl13" class="lbl_norm2" alert="lbl_alrt2">Bilet Numarası: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_return_ticket_number" name="inp_return_ticket_number" label="div_lbl13" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_ticket_price" style="height: 58px;" hidden>
												<div id="div_lbl14" class="lbl_norm2" alert="lbl_alrt2">Fiyat Bilgisi:</div>
												<div><input type="text" id="inp_return_ticket_price" name="inp_return_ticket_price" label="div_lbl14" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_car_license_plate" style="height: 58px;" hidden>
												<div id="div_lbl15" class="lbl_norm2" alert="lbl_alrt2">Araç Plakası: <font style="color: red;">*</font></div>
												<div><input type="text" id="inp_return_car_license_plate" name="inp_return_car_license_plate" label="div_lbl15" area="area3" action="go" class="inp" /></div>
											</div>
											<div id="div_return_date" style="height: 58px;" hidden>
												<div id="div_lbl16" class="lbl_norm2" alert="lbl_alrt2">Dönüş Tarihi: <font style="color: red;">*</font></div>
												<div><input type="datetime-local" date-group="1" date-type="end_date" id="inp_return_date" name="inp_return_date" label="div_lbl16" area="area3" action="go" class="inp" /></div>
											</div>
										</div>
										<div style="height: 8px;"></div>
									</div>
								</div>
							</div>
						</div>
						<div id="div_accommodation">
							<div style="height: 20px;"></div>
							<div align="left" class="blue_line">Konaklama Bilgileri</div>
							<div style="height: 10px;"></div>
							<div align="left" class="lbl_norm2">Otel</div>
							<div style="display: flex; border: solid 1px blue;">
								<div align="center" style="width: 49%;">
									<div style="height: 12px;"></div>
									<div align="left" style="width: 90%;">
										<div style="height: 58px;">
											<div id="div_lbl17" class="lbl_norm2" alert="lbl_alrt2">Giriş Tarihi: <font style="color: red;">*</font></div>
											<div><input type="datetime-local" date-group="2" date-type="start_date" id="inp_check-in_date" name="inp_check-in_date" label="div_lbl17" area="area3" action="go" class="inp" /></div>
										</div>
										<div style="height: 58px;">
											<div id="div_lbl18" class="lbl_norm2" alert="lbl_alrt2">Adı: <font style="color: red;">*</font></div>
											<div><input type="text" id="inp_hotel_name" name="inp_hotel_name" label="div_lbl18" area="area3" action="go" class="inp" /></div>
										</div>
									</div>
									<div style="height: 8px;"></div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div style="height: 12px;"></div>
									<div align="left" style="width: 90%;">
										<div style="height: 58px;">
											<div id="div_lbl19" class="lbl_norm2" alert="lbl_alrt2">Çıkış Tarihi: <font style="color: red;">*</font></div>
											<div><input type="datetime-local" date-group="2" date-type="end_date" id="inp_check-out_date" name="inp_check-out_date" label="div_lbl19" area="area3" action="go" class="inp" /></div>
										</div>
									</div>
									<div style="height: 8px;"></div>
								</div>
							</div>
						</div>
						<div id="div_save">
							<div style="height: 10px;"></div>
							<div style="display: flex; height: 38px;">
								<div align="right" style="width: 100%;">
									<input type="button" id="btn_add_reservation" area="area3" action="warn" class="btn" value="Kaydet ⮟" onClick="add_reservation($(this));" />
								</div>
								<div style="width: 15px;"></div>
								<div align="right" style="width: 100%;">
									<input type="button" id="btn_back_page" area="area3" action="warn" class="btn" value="Geri ⮝" onclick="parent.load_page($(this), '', '');" />
								</div>
							</div>
						</div>
						<div style="height: 5px;"></div>
					</form>
				</div>
				<div id="div_approve_page" hidden></div>
			</div>
		</div>
	</body>
	<script type="text/javascript">
		window.onload = function() {
			$('#div_main_area').css('margin-left', ($(window).width() - $('#div_main_area').width()) / 2);
			$('#div_main_area').show();
		};
		$('#div_approve_page').load('./reservation_approval_form.php');
	</script>
</html>