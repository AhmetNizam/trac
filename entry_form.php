<?php
	require("./library.php");

	unset($_SESSION['request']);
?>
<!doctype HTML 4.01 Transitional>
<html xmlns="http://www.w3.org/2001/XMLSchema">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=750">

		<title>Ulaşım ve Konaklama - Başvuru Formu</title>

		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

		<script src="./js/jquery-3.7.1.js"></script>
		<script src="./js/jquery-ui-1.13.2/jquery-ui.js"></script>

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

			$(document).ready(function() {
				//$('#inp_birthdate').inputmask("gg.aa.yyyy");
				$('#inp_identityno').inputmask("99999999999");
				$('#inp_phone').inputmask("9(999) 999 99 99");

				$(document).on('mousedown', function(event) {
					rgfocus(event);
				});

				$(':input').on('focus', function(event) {
					rgfocus(event);
				});

				$(':input').on('blur', function(event) {
					if(!$(event.target).attr('readonly')) {
						if(($(event.target).attr('id') == 'inp_name' || $(event.target).attr('id') == 'inp_surname' || $(event.target).attr('id') == 'inp_birthdate') &&
							$('#inp_name').val() != '' && $('#inp_surname').val() != '' && $('#inp_birthdate').val() != '') {
							get_traveler_info('name', 'name=' + $('#inp_name').val() + '&surname=' + $('#inp_surname').val() + '&birthdate=' + $('#inp_birthdate').val());
						} else if($(event.target).attr('id') == 'inp_identityno' && $(event.target).val() != '') {
							get_traveler_info('identityno', 'identityno=' + $(event.target).val());
						} else if($(event.target).attr('id') == 'inp_passportno' && $(event.target).val() != '') {
							get_traveler_info('passportno', 'passportno=' + $(event.target).val());
						} else if($(event.target).attr('id') == 'inp_phone' && $(event.target).val() != '') {
							get_traveler_info('phone', 'phone=' + $(event.target).val());
						} else if($(event.target).attr('id') == 'inp_mail' && $(event.target).val() != '') {
							get_traveler_info('mail', 'mail=' + $(event.target).val());
						}
					}
				});

				$(':input').on('keydown', function(event) {
					if(event.target.tagName.toLowerCase != 'textarea' && $(event.target).attr('type').toLowerCase != 'button' && event.which == '13') {
						check_form($(event.target));
					}
				});

				$('select').on('change', function(event) {
					var item = $(event.target).attr('id').replace('sel', '');
					$('#inp' + item).val($('#sel' + item + ' option:selected').text());
				});

				$('#inp_name').on('input', function(event) {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_surname').on('input', function(event) {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_mail').on('input', function(event) {
					var value = $(this).val().replace(/ç/g, "c").replace(/Ç/g, "c")
											 .replace(/ğ/g, "g").replace(/Ğ/g, "g")
											 .replace(/İ/g, "i").replace(/ı/g, "i")
											 .replace(/ö/g, "o").replace(/Ö/g, "o")
											 .replace(/ş/g, "s").replace(/Ş/g, "s")
											 .replace(/ü/g, "u").replace(/Ü/g, "u")
											 .toLowerCase();
					$(this).val(value);
				});

				$('#inp_from_city').on('input', function(event) {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_from_city').on('keyup', function(event) {
					set_reason();
				});

				$('#inp_to_city').on('input', function(event) {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_to_city').on('keyup', function(event) {
					set_reason();
				});

			});

			function isdescendant(parent, child) {
				let node = child.parentNode;
				while(node != null) {
					if(node == parent) {
						return true;
					}
					node = node.parentNode;
				}
				return false;
			}

			function rgfocus(event) {
				$('[id^="div_radio_group"]').each(function() {
					if(this == event.target || isdescendant(this, event.target)) {
						$(this).parent().css('border-color', 'black');
						$(this).css('border-color', 'black');
					} else {
						$(this).parent().css('border-color', '#767676');
						$(this).css('border-color', 'transparent');
					}
				});
			}

			function get_traveler_info(search_type, params) {
				var postdata = 'type=' + search_type + '&' + params;
				$.getJSON('./get_traveler_info_from_db.php?' + postdata,
					function(data) {
						if(data.Rows) {
							var rowData = data.Rows[0];
							if(rowData.status == 1) {
								set_traveler_info(rowData);
							} else if(search_type == 'mail') {
								$.getJSON('./get_traveler_info_from_ad.php?' + postdata,
									function(data) {
										if(data.Rows) {
											var rowData = data.Rows[0];
											if(rowData.status == 1) {
												set_traveler_info(rowData);
											} else {
												toggle_visibility([$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()], [$('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent()]);
											}
										}
									}
								)
							}
						}
					}
				).done(
					function () {
						check_form($('#sel_requester_type'));
						
					}
				);
			}

			function identityno_checksum(id) {
				if((parseInt(id[0]) + parseInt(id[1]) + parseInt(id[2]) + parseInt(id[3]) + parseInt(id[4]) + parseInt(id[5]) + parseInt(id[6]) + parseInt(id[7]) + parseInt(id[8]) + parseInt(id[9])) % 10 != parseInt(id[10])) {
					return false;
				}
				if(((parseInt(id[0]) + parseInt(id[2]) + parseInt(id[4]) + parseInt(id[6]) + parseInt(id[8])) * 7 + (parseInt(id[1]) + parseInt(id[3]) + parseInt(id[5]) + parseInt(id[7])) * 9) % 10 != parseInt(id[9])) {
					return false;
				}
				if((parseInt(id[0]) + parseInt(id[2]) + parseInt(id[4]) + parseInt(id[6]) + parseInt(id[8])) * 8 % 10 != parseInt(id[10])) {
					return false;
				}
				return true;
			}

			function generateUUID() {
				var d1 = new Date().getTime();//Timestamp
				var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now()*1000)) || 0; //Time in microseconds since page-load or 0 if unsupported
				return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
					var r = Math.random() * 16; //random number between 0 and 16
					if(d1 > 0){ //Use timestamp until depleted
						r = (d1 + r)%16 | 0;
						d1 = Math.floor(d1/16);
					} else { //Use microseconds since page-load if supported
						r = (d2 + r)%16 | 0;
						d2 = Math.floor(d2/16);
					}
					return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
				});
			}

			function toggle_visibility(visible_items, unvisible_items) {
				$.each(unvisible_items, function() { $(this).hide(); });
				$.each(visible_items, function() { $(this).show(); });
			}

			function toggle_writability(writable_items, readonly_items) {
				$.each(writable_items, function() { $(this).attr('readonly', false); });
				$.each(readonly_items, function() { $(this).attr('readonly', true); });
			}

			function reset_values(items) {
				$.each(items, function() { $(this).val(''); });
			}

			function reset_personel_info() {
				reset_values([$('#inp_name'), $('#inp_surname'), $('#inp_birthdate'), $('#inp_identityno'), $('#inp_passportno'), $('#inp_phone'), $('#inp_mail')]);
				reset_values([$('#inp_location'), $('#inp_position'), $('#inp_department'), $('#sel_location'), $('#sel_position'), $('#sel_department')]);
			}

			function reset_writability_attribute() {
				toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_birthdate'), $('#inp_identityno'), $('#inp_passportno'), $('#inp_phone'), $('#inp_mail')], []);
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

			function check_form_data(item, section) {
				var result = true;
				var action = item.attr('action');
				var xmlNodes = $.merge(xmlDoc.find('input[id="' + item.attr('id') + '"]'), xmlDoc.find(section).children());

				xmlNodes.each(
					function() {
						if(eval($(this).children('condition').text())) {
							if(action == 'warn') {
								alert($(this).children('alert').text());
								var label_item = $('#' + $('#' + $(this).attr('id')).attr('label'));
								label_item.attr('class', label_item.attr('alert'));
							} else if($(this).children('forced_alert').text() == '1') {
								alert($(this).children('alert').text());
							}
							$('#' + $(this).attr('id')).focus();
							result = false;
							return result;
						}
					}
				);
				return result;
			}

			function check_form(item) {
				reset_alerts();

				var area = item.attr('area');

				if(area == 'area1') {
					if(check_form_data(item, 'section1')) {
						$('#btn_add_traveler').focus();
						return true;
					} else {
						return false;
					}
				} else if(area == 'area2') {
					if($('#rb_route1').prop('checked')) {
						if(!check_form_data(item, 'section2')) {
							return false;
						}
					} else if($('#rb_route2').prop('checked')) {
						if(!check_form_data(item, 'section3')) {
							return false;
						}
					}
					if(!check_form_data(item, 'section4')) {
						return false;
					}
					if(!check_form_data(item, 'section5')) {
						return false;
					}
					if($('#rb_trac1').prop('checked') || $('#rb_trac2').prop('checked')) {
						if(!check_form_data(item, 'section6')) {
							return false;
						}
					}
					if($('#rb_trac1').prop('checked') || $('#rb_trac3').prop('checked')) {
						if(!check_form_data(item, 'section7')) {
							return false;
						}
					}
					$('#btn_add_request').focus();
					return true;
				}
			}

			function set_traveler_info(data) {
				toggle_visibility([$('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent()], [$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()]);

				$('#inp_name').val(data.name ? data.name : '');
				$('#inp_surname').val(data.surname ? data.surname : '');
				$('#inp_birthdate').val(data.birthdate ? data.birthdate : '');
				$('#inp_identityno').val(data.identityno ? data.identityno : '');
				$('#inp_passportno').val(data.passportno ? data.passportno : '');
				$('#inp_phone').val(data.phone ? data.phone : '');
				$('#inp_mail').val(data.mail ? data.mail : '');
				$('#inp_position').val(data.positionname ? data.positionname : '');
				$('#inp_department').val(data.departmentname ? data.departmentname : '');
				$('#inp_location').val(data.locationname ? data.locationname : '');
				$('#sel_position').val(data.positionid ? data.positionid : '');
				$('#sel_department').val(data.departmentid ? data.departmentid : '');
				$('#sel_location').val(data.locationid ? data.locationid : '');
			}

			function set_requester_type(item) {
				reset_personel_info();
				reset_writability_attribute();

				if(item.val() == '') {
					toggle_visibility([], [$('#div_personal_info'), $('#div_staff_info'), $('#div_add_button')]);
				} else if(item.val() == '1') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent(), $('#div_add_button')], [$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()]);
					toggle_writability([], [$('#inp_name'), $('#inp_surname'), $('#inp_mail')]);
					get_traveler_info('mail', 'mail=<?php echo $_SESSION['user_info']['mail']; ?>');
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent(), $('#div_add_button')], [$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()]);
					toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_mail')], []);
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_personal_info'), $('#div_add_button')], [$('#div_staff_info')]);
					toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_mail')], []);
				}

				check_form(item);
			}

			function set_travel_route(item) {
				reset_values([$('#sel_from_country'), $('#sel_to_country'), $('#sel_from_location'), $('#sel_to_location'), $('#inp_from_city'), $('#inp_to_city'), $('#sel_from_city'), $('#sel_to_city')]);

				$('#sel_to_country').html($('#sel_from_country').html());
				$('#sel_to_city').html($('#sel_from_city').html());

				if(item.attr('id') == 'rb_route1') {
					$('#inp_route').val('1');
					$('#inp_route_name').val('Yurt İçi');
					$('#fnt_route').html('Yurt İçi');
					toggle_visibility([], [$('#div_passport')]);
					$('#th_trv_list_passport').hide();
					$('#div_lbl12').html('Nereden: <font style="color: red;">*</font>');
					$('#div_lbl15').html('Nereye: <font style="color: red;">*</font>');
					fill_location_list('220', $('#sel_from_location')).then(data => {
						$('#sel_from_country').val('220');
						$('#sel_to_country').val('220');
						if($('#sel_from_location').children().length > 0) {
							$('#sel_to_location').html($('#sel_from_location').html());
							toggle_visibility([$('#div_from_location'), $('#div_to_location')], [$('#div_from_country'), $('#div_to_country'), $('#div_from_city'), $('#div_to_city')]);
						} else {
							toggle_visibility([$('#div_from_city'), $('#div_to_city')], [$('#div_from_country'), $('#div_to_country'), $('#div_from_location'), $('#div_to_location')]);
						}
					});
				} else if(item.attr('id') == 'rb_route2') {
					$('#inp_route').val('2');
					$('#inp_route_name').val('Yurt Dışı');
					$('#fnt_route').html('Yurt Dışı');
					toggle_visibility([$('#div_passport')], []);
					$('#th_trv_list_passport').show();
					$('#div_lbl12').html('Lokasyon: <font style="color: red;">*</font>');
					$('#div_lbl15').html('Lokasyon: <font style="color: red;">*</font>');
					toggle_visibility([$('#div_from_country'), $('#div_to_country')], [$('#div_from_location'), $('#div_to_location'), $('#div_from_city'), $('#div_to_city')]);
				}
			}

			function add_traveler(item) {
				if(item.val() == 'Güncelle ⭮') {
					item.val('Ekle ✚');
					$('#sel_requester_type').prop('disabled', false);
				}

				if(!check_form(item)) {
					return false;
				}

				if($('#inp_uuid').val() == '') {
					$('#inp_uuid').val(generateUUID());
				}

				var content = '';
				var uuid = $('#inp_uuid').val();
				var formData = $('#form1').serialize();

				$.getJSON('./add_traveler.php?' + formData,
					function(data) {
						if(data.Rows) {
							var rowData = data.Rows[0];
							if(rowData.status == '0') {
								alert('Bu kişi listeye daha önce eklenmiş.');
							} else if(rowData.status == '1') {
								content =	'	<tr id="' + uuid + '-tr1" class="trv_tbody">' +
											'		<td id="' + uuid + '-request_owner" hidden>' + $('#sel_requester_type').val() + '</td>' +
											'		<td id="' + uuid + '-name" class="trv_cell_tb">' + rowData.name + '</td>' +
											'		<td id="' + uuid + '-surname" class="trv_cell_tb">' + rowData.surname + '</td>' +
											'		<td id="' + uuid + '-birthdate" class="trv_cell_tb">' + rowData.birthdate + '</td>' +
											'		<td id="' + uuid + '-identityno" class="trv_cell_tb">' + rowData.identityno + '</td>';

								if($('#inp_route').val() == '2') {
									content +=	'		<td id="' + uuid + '-passportno" class="trv_cell_tb">' + rowData.passportno + '</td>';
								}

								content +=	'		<td id="' + uuid + '-phone" class="trv_cell_tb">' + rowData.phone + '</td>' +
											'		<td id="' + uuid + '-mail" class="trv_cell_tb">' + rowData.mail + '</td>' +
											'		<td id="' + uuid + '-position" class="trv_cell_tb">' + rowData.position + '</td>' +
											'		<td id="' + uuid + '-positionid" hidden>' + rowData.positionid + '</td>' +
											'		<td id="' + uuid + '-department" class="trv_cell_tb">' + rowData.department + '</td>' +
											'		<td id="' + uuid + '-departmentid" hidden>' + rowData.departmentid + '</td>' +
											'		<td id="' + uuid + '-location" class="trv_cell_tb">' + rowData.location + '</td>' +
											'		<td id="' + uuid + '-locationid" hidden>' + rowData.locationid + '</td>' +
											'	</tr>';

								$('#tb_traveler_list').append(content);

								content =	'	<tr id="' + uuid + '-tr2" class="trv_tbody">' +
											'		<td class="trv_cell_th"><img src="./images/edit.png" class="trv_cell_img" title="Düzenle" onClick="edit_traveler(\'' + uuid + '\');" /></td>' +
											'		<td class="trv_cell_th"><img src="./images/delete.png" class="trv_cell_img" title="Sil" onClick="del_traveler(\'' + uuid + '\');" /></td>' +
											'	</tr>';

								$('#tb_process_list').append(content);

								$('#rb_route1').prop('disabled', true);
								$('#rb_route2').prop('disabled', true);

								$('#sel_requester_type').val('');
								set_requester_type($('#sel_requester_type'));
							} else if(rowData.status == '2') {
								$('#' + uuid + '-name').html(rowData.name);
								$('#' + uuid + '-surname').html(rowData.surname);
								$('#' + uuid + '-birthdate').html(rowData.birthdate);
								$('#' + uuid + '-identityno').html(rowData.identityno);
								$('#' + uuid + '-passportno').html(rowData.passportno);
								$('#' + uuid + '-phone').html(rowData.phone);
								$('#' + uuid + '-mail').html(rowData.mail);
								$('#' + uuid + '-position').html(rowData.position);
								$('#' + uuid + '-positionid').html(rowData.positionid);
								$('#' + uuid + '-department').html(rowData.department);
								$('#' + uuid + '-departmentid').html(rowData.departmentid);
								$('#' + uuid + '-location').html(rowData.location);
								$('#' + uuid + '-locationid').html(rowData.locationid);

								alert('"' + rowData.name + ' ' + rowData.surname + '" için bilgiler güncellendi.');

								$('#sel_requester_type').prop('disabled', false);
								$('#sel_requester_type').val('');
								set_requester_type($('#sel_requester_type'));
							}
						}
					}
				).done(
					function() {
						toggle_visibility([$('#div_traveler_list')], []);
						$('#inp_uuid').val('');
						$('#btn_continue').focus();

						return true;
					}
				);
			}

			function edit_traveler(uuid) {
				$('#sel_requester_type').val($('#' + uuid + '-request_owner').html());
				$('#sel_requester_type').prop('disabled', true);
				$('#div_traveler_type').show();
				set_requester_type($('#sel_requester_type'));

				$('#inp_uuid').val(uuid);
				$('#inp_name').val($('#' + uuid + '-name').html());
				$('#inp_surname').val($('#' + uuid + '-surname').html());
				var birthdate = $('#' + uuid + '-birthdate').html().split('.');
				$('#inp_birthdate').val(birthdate[2] + '-' + birthdate[1] + '-' + birthdate[0]);
				$('#inp_identityno').val($('#' + uuid + '-identityno').html());
				$('#inp_passportno').val($('#' + uuid + '-passportno').html());
				$('#inp_phone').val($('#' + uuid + '-phone').html());
				$('#inp_mail').val($('#' + uuid + '-mail').html());
				$('#inp_position').val($('#' + uuid + '-position').html());
				$('#sel_position').val($('#' + uuid + '-positionid').html());
				$('#inp_department').val($('#' + uuid + '-department').html());
				$('#sel_department').val($('#' + uuid + '-departmentid').html());
				$('#inp_location').val($('#' + uuid + '-location').html());
				$('#sel_location').val($('#' + uuid + '-locationid').html());

				$('#btn_add_traveler').val('Güncelle ⭮');
			}

			function del_traveler(uuid) {
				if(confirm('"' + $('#' + uuid + '-name').html() + ' ' + $('#' + uuid + '-surname').html() + '" yolcu listesinden çıkarılacak.\nDevam etmek istiyor musunuz?')) {
					if($('#tb_traveler_list').children().length == 1) {
						$('#sel_requester_type').prop('disabled', false);
						$('#rb_route1').prop('disabled', false);
						$('#rb_route2').prop('disabled', false);
					}
					$('#' + uuid + '-tr1').remove();
					$('#' + uuid + '-tr2').remove();

					$.getJSON('./del_traveler.php?uuid=' + uuid);
				}
			}

			function set_travel_info(item) {
				if(item.val() == 'Devam ⮞') {
					item.val('Geri ⮝');
					toggle_visibility([$('#div_route'), $('#div_travel_info')], [$('#div_traveler_type'), $('#div_personal_info'), $('#div_staff_info'), $('#div_add_button')]);
					check_form(item);
				} else if(item.val() == 'Geri ⮝') {
					item.val('Devam ⮞');
					toggle_visibility([$('#div_traveler_type')], []);
				}
			}

			function fill_location_list(country_id, location_item) {
				return new Promise((resolve, reject) => {
					var content = '';
					$.getJSON('./get_location_by_country.php?country_id=' + country_id,
						function(data) {
							if(data.Rows.length > 0) {
								content = '<option value="">-- Seçiniz --</option>';
								content += '<option value="0">Diğer</option>';

								for(var i = 0; i < data.Rows.length; i++) {
									var rowData = data.Rows[i];
									content += '<option value="' + rowData.id + '">' + rowData.name + '</option>';
								}
							}
							location_item.html(content);
							resolve(data);
						}
					).fail(function(error) { reject(error); })
				});
			}

			function set_country(ctrl_item, div_item1, location_item, div_item2, input_item, select_item) {
				reset_values([location_item, input_item, select_item]);
				input_item.prop('readonly', false);
				if(ctrl_item.val().length > 0) {
					fill_location_list(ctrl_item.val(), location_item).then(data => {
						if(location_item.children().length > 0) {
							toggle_visibility([div_item1], [div_item2]);
						} else {
							toggle_visibility([div_item2, input_item.parent()], [div_item1, select_item.parent()]);
						}
					});
				} else {
					toggle_visibility([], [div_item1, div_item2]);
				}
			}

			function set_location(ctrl_item, country_item, div_item, input_item, select_item) {
				input_item.val('');
				select_item.val('');
				if(ctrl_item.val() == '') {
					div_item.hide();
				} else if(ctrl_item.val() == '0') {
					if($('#rb_route1').prop('checked') == true || country_item.val() == '220') {
						toggle_visibility([div_item, select_item.parent()], [input_item.parent()]);
					} else {
						toggle_visibility([div_item, input_item.parent()], [select_item.parent()]);
						input_item.prop('readonly', false);
					}
				} else {
					$.getJSON('./get_city_by_location.php?location_id=' + ctrl_item.val(),
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								input_item.val(rowData.name);
								select_item.val(rowData.id);
							}
						}
					).done(
						function() {
							toggle_visibility([div_item, input_item.parent()], [select_item.parent()]);
							input_item.prop('readonly', true);
							set_reason();
						}
					);
				}
			}

			function set_city() {
				set_reason();
			}

			function set_reason() {
				if( ($('#inp_from_city').val() != '' || ($('#sel_from_city').val() !== null ? $('#sel_from_city').val() : '' != '')) &&
					($('#inp_to_city').val() != '' || ($('#sel_to_city').val() !== null ? $('#sel_to_city').val() : '' != '')) ) {
					$('#div_travel_reason').show();
				}
			}

			function set_transfer_need_situation(item) {
				if(item.attr('id') == 'rb_tns1') {
					$('#inp_transfer_need_situation').val('1');
					$('#inp_transfer_need_situation_name').val('Var');
					toggle_visibility([$('#div_nftd')], []);
				} else if(item.attr('id') == 'rb_tns2') {
					$('#inp_transfer_need_situation').val('2');
					$('#inp_transfer_need_situation_name').val('Yok');
					toggle_visibility([], [$('#div_nftd')]);
				}
			}

			function add_request(item) {
				if(!check_form(item)) {
					return false;
				}

				var formData = $('#form1').serialize();

				$.getJSON('./add_request.php?' + formData).done(
					function() {
						$('#div_form_page').hide();
						$('#div_approve_page').load('./approval_form.php');
						$('#div_approve_page').show();
					}
				);
			}

			function save_request() {
				$.getJSON('./save_request.php',
					function(data) {
						if(data.Rows) {
							var rowData = data.Rows[0];
							if(rowData.status == '1') {
								send_mail();
							} else {
								alert('Talep kaydedilirken bir hata oluştu!');
							}
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
				doc.getElementById('div_completion').remove();
				doc.getElementById('div_preview').style.display = 'block';
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

				$.ajax({
					type: "POST",
					url: "./send_mail.php",
					data: { mailBody: htmlText },	// Gönderilecek veri
					success: function(response) {
						toggle_visibility([$('#div_completion')], [$('#div_preview')]);
						console.log("İstek başarılı.");
						console.log("Sunucudan gelen yanıt:", response);
					},
					error: function(xhr, status, error) {
						alert('Talep kaydedildi ancak mail gönderilirken bir hata oluştu!');
						console.error("İstek kayıt sırasında bir hata oluştu:", error);
					}
				});
			}

			function save_and_send_request() {
				var q = save_request();
				alert(q);
				if(q) {
					if(send_mail()) {
						toggle_visibility([$('#div_completion')], [$('#div_preview')]);
					} else {
						alert('Talep kaydedildi ancak mail gönderilirken bir hata oluştu!');
					}
				} else {
					alert('Talep kaydedilirken bir hata oluştu!');
				}
			}

			function cancel_request() {
				if(confirm('Talep iptal edilecek.\nDevam etmek istiyor musunuz?')) {
					window.open('./entry_form.php', '_self');
				}
			}

		</script>
	</head>
	<body style="background-image: url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAAAAACoWZBhAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKbWlUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4gPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iQWRvYmUgWE1QIENvcmUgNS42LWMxNDUgNzkuMTYzNDk5LCAyMDE4LzA4LzEzLTE2OjQwOjIyICAgICAgICAiPiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPiA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgeG1sbnM6cGhvdG9zaG9wPSJodHRwOi8vbnMuYWRvYmUuY29tL3Bob3Rvc2hvcC8xLjAvIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtbG5zOnRpZmY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vdGlmZi8xLjAvIiB4bWxuczpleGlmPSJodHRwOi8vbnMuYWRvYmUuY29tL2V4aWYvMS4wLyIgeG1wTU06RG9jdW1lbnRJRD0iYWRvYmU6ZG9jaWQ6cGhvdG9zaG9wOjA5YzY0MjliLTE1ZTEtNTE0My04ODE3LTlmNWZjNTBjYTM5MyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDoyZGFlMTkyMS0zNjJkLWE2NDItOTFiYi02ZjZhNzZiODA1OWEiIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0iMjhDQkZGMjdBODY2MDg1NjU5M0JFNjE1QkM1RDE0REMiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiBwaG90b3Nob3A6Q29sb3JNb2RlPSIxIiBwaG90b3Nob3A6SUNDUHJvZmlsZT0iRG90IEdhaW4gMTUlIiB4bXA6Q3JlYXRlRGF0ZT0iMjAxNS0wMS0yNlQxNzozMTo0MyswMjowMCIgeG1wOk1vZGlmeURhdGU9IjIwMTktMTEtMDVUMDA6NDA6NTgrMDM6MDAiIHhtcDpNZXRhZGF0YURhdGU9IjIwMTktMTEtMDVUMDA6NDA6NTgrMDM6MDAiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKSIgdGlmZjpJbWFnZVdpZHRoPSIxMCIgdGlmZjpJbWFnZUxlbmd0aD0iMTAiIHRpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj0iMiIgdGlmZjpPcmllbnRhdGlvbj0iMSIgdGlmZjpTYW1wbGVzUGVyUGl4ZWw9IjMiIHRpZmY6WFJlc29sdXRpb249IjcyMDAwMC8xMDAwMCIgdGlmZjpZUmVzb2x1dGlvbj0iNzIwMDAwLzEwMDAwIiB0aWZmOlJlc29sdXRpb25Vbml0PSIyIiBleGlmOkV4aWZWZXJzaW9uPSIwMjIxIiBleGlmOkNvbG9yU3BhY2U9IjEiIGV4aWY6UGl4ZWxYRGltZW5zaW9uPSIxMCIgZXhpZjpQaXhlbFlEaW1lbnNpb249IjEwIj4gPHhtcE1NOkhpc3Rvcnk+IDxyZGY6U2VxPiA8cmRmOmxpIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6YmI5ZWZlOWYtODViZS0xOTRjLTg2OTEtMWFkMTVkZGQwYzJmIiBzdEV2dDp3aGVuPSIyMDE1LTAyLTE5VDEzOjEzOjU2KzAyOjAwIiBzdEV2dDpzb2Z0d2FyZUFnZW50PSJBZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJjb252ZXJ0ZWQiIHN0RXZ0OnBhcmFtZXRlcnM9ImZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmciLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249ImRlcml2ZWQiIHN0RXZ0OnBhcmFtZXRlcnM9ImNvbnZlcnRlZCBmcm9tIGltYWdlL2pwZWcgdG8gaW1hZ2UvcG5nIi8+IDxyZGY6bGkgc3RFdnQ6YWN0aW9uPSJzYXZlZCIgc3RFdnQ6aW5zdGFuY2VJRD0ieG1wLmlpZDo5MjM3NjAzOS0xZDQ3LWI5NDYtOThlOC02MGFmZGNhNDk4Y2IiIHN0RXZ0OndoZW49IjIwMTUtMDItMTlUMTM6MTM6NTYrMDI6MDAiIHN0RXZ0OnNvZnR3YXJlQWdlbnQ9IkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cykiIHN0RXZ0OmNoYW5nZWQ9Ii8iLz4gPHJkZjpsaSBzdEV2dDphY3Rpb249InNhdmVkIiBzdEV2dDppbnN0YW5jZUlEPSJ4bXAuaWlkOjJkYWUxOTIxLTM2MmQtYTY0Mi05MWJiLTZmNmE3NmI4MDU5YSIgc3RFdnQ6d2hlbj0iMjAxOS0xMS0wNVQwMDo0MDo1OCswMzowMCIgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTkgKFdpbmRvd3MpIiBzdEV2dDpjaGFuZ2VkPSIvIi8+IDwvcmRmOlNlcT4gPC94bXBNTTpIaXN0b3J5PiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpiYjllZmU5Zi04NWJlLTE5NGMtODY5MS0xYWQxNWRkZDBjMmYiIHN0UmVmOmRvY3VtZW50SUQ9IjI4Q0JGRjI3QTg2NjA4NTY1OTNCRTYxNUJDNUQxNERDIiBzdFJlZjpvcmlnaW5hbERvY3VtZW50SUQ9IjI4Q0JGRjI3QTg2NjA4NTY1OTNCRTYxNUJDNUQxNERDIi8+IDx0aWZmOkJpdHNQZXJTYW1wbGU+IDxyZGY6U2VxPiA8cmRmOmxpPjg8L3JkZjpsaT4gPHJkZjpsaT44PC9yZGY6bGk+IDxyZGY6bGk+ODwvcmRmOmxpPiA8L3JkZjpTZXE+IDwvdGlmZjpCaXRzUGVyU2FtcGxlPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PhJAkb8AAABTSURBVAiZBcFBDsIwEANA25sitYL/P5ITF6iSrM0M38cZWsHWC82A5uDEt1RKl+LHWssYkaGnCe8oJq45i5TQ7QvblIgCxv0B7z4CgBOiDKLN3x+goiriuLS6twAAAABJRU5ErkJggg==');" topmargin="20">
		<div align="center">
			<div class="main_frame" style="background-image: url('data:image/png;base64,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/GMzLdsAAEEtaVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjUtYzAyMSA3OS4xNTQ5MTEsIDIwMTMvMTAvMjktMTE6NDc6MTYgICAgICAgICI+CiAgIDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+CiAgICAgIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICAgICAgICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgICAgICAgICB4bWxuczpzdEV2dD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlRXZlbnQjIgogICAgICAgICAgICB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIKICAgICAgICAgICAgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIgogICAgICAgICAgICB4bWxuczpwaG90b3Nob3A9Imh0dHA6Ly9ucy5hZG9iZS5jb20vcGhvdG9zaG9wLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgICAgICAgICAgeG1sbnM6dGlmZj0iaHR0cDovL25zLmFkb2JlLmNvbS90aWZmLzEuMC8iCiAgICAgICAgICAgIHhtbG5zOmV4aWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20vZXhpZi8xLjAvIj4KICAgICAgICAgPHhtcE1NOkRvY3VtZW50SUQ+MjhDQkZGMjdBODY2MDg1NjU5M0JFNjE1QkM1RDE0REM8L3htcE1NOkRvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpJbnN0YW5jZUlEPnhtcC5paWQ6OGRlMzgxNjAtNGQxYi0xNzQxLTk3YmYtYzhjMTBkMDE2OGU3PC94bXBNTTpJbnN0YW5jZUlEPgogICAgICAgICA8eG1wTU06T3JpZ2luYWxEb2N1bWVudElEPjI4Q0JGRjI3QTg2NjA4NTY1OTNCRTYxNUJDNUQxNERDPC94bXBNTTpPcmlnaW5hbERvY3VtZW50SUQ+CiAgICAgICAgIDx4bXBNTTpIaXN0b3J5PgogICAgICAgICAgICA8cmRmOlNlcT4KICAgICAgICAgICAgICAgPHJkZjpsaSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDphY3Rpb24+c2F2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0Omluc3RhbmNlSUQ+eG1wLmlpZDpiYjllZmU5Zi04NWJlLTE5NGMtODY5MS0xYWQxNWRkZDBjMmY8L3N0RXZ0Omluc3RhbmNlSUQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDp3aGVuPjIwMTUtMDItMTlUMTM6MTM6NTYrMDI6MDA8L3N0RXZ0OndoZW4+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpzb2Z0d2FyZUFnZW50PkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cyk8L3N0RXZ0OnNvZnR3YXJlQWdlbnQ+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDpjaGFuZ2VkPi88L3N0RXZ0OmNoYW5nZWQ+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5jb252ZXJ0ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+ZnJvbSBpbWFnZS9qcGVnIHRvIGltYWdlL3BuZzwvc3RFdnQ6cGFyYW1ldGVycz4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPmRlcml2ZWQ8L3N0RXZ0OmFjdGlvbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnBhcmFtZXRlcnM+Y29udmVydGVkIGZyb20gaW1hZ2UvanBlZyB0byBpbWFnZS9wbmc8L3N0RXZ0OnBhcmFtZXRlcnM+CiAgICAgICAgICAgICAgIDwvcmRmOmxpPgogICAgICAgICAgICAgICA8cmRmOmxpIHJkZjpwYXJzZVR5cGU9IlJlc291cmNlIj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmFjdGlvbj5zYXZlZDwvc3RFdnQ6YWN0aW9uPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6aW5zdGFuY2VJRD54bXAuaWlkOjkyMzc2MDM5LTFkNDctYjk0Ni05OGU4LTYwYWZkY2E0OThjYjwvc3RFdnQ6aW5zdGFuY2VJRD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OndoZW4+MjAxNS0wMi0xOVQxMzoxMzo1NiswMjowMDwvc3RFdnQ6d2hlbj4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OnNvZnR3YXJlQWdlbnQ+QWRvYmUgUGhvdG9zaG9wIENDIChXaW5kb3dzKTwvc3RFdnQ6c29mdHdhcmVBZ2VudD4KICAgICAgICAgICAgICAgICAgPHN0RXZ0OmNoYW5nZWQ+Lzwvc3RFdnQ6Y2hhbmdlZD4KICAgICAgICAgICAgICAgPC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGkgcmRmOnBhcnNlVHlwZT0iUmVzb3VyY2UiPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6YWN0aW9uPnNhdmVkPC9zdEV2dDphY3Rpb24+CiAgICAgICAgICAgICAgICAgIDxzdEV2dDppbnN0YW5jZUlEPnhtcC5paWQ6OGRlMzgxNjAtNGQxYi0xNzQxLTk3YmYtYzhjMTBkMDE2OGU3PC9zdEV2dDppbnN0YW5jZUlEPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6d2hlbj4yMDE2LTAyLTE2VDExOjQ3OjE3KzAyOjAwPC9zdEV2dDp3aGVuPgogICAgICAgICAgICAgICAgICA8c3RFdnQ6c29mdHdhcmVBZ2VudD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC9zdEV2dDpzb2Z0d2FyZUFnZW50PgogICAgICAgICAgICAgICAgICA8c3RFdnQ6Y2hhbmdlZD4vPC9zdEV2dDpjaGFuZ2VkPgogICAgICAgICAgICAgICA8L3JkZjpsaT4KICAgICAgICAgICAgPC9yZGY6U2VxPgogICAgICAgICA8L3htcE1NOkhpc3Rvcnk+CiAgICAgICAgIDx4bXBNTTpEZXJpdmVkRnJvbSByZGY6cGFyc2VUeXBlPSJSZXNvdXJjZSI+CiAgICAgICAgICAgIDxzdFJlZjppbnN0YW5jZUlEPnhtcC5paWQ6YmI5ZWZlOWYtODViZS0xOTRjLTg2OTEtMWFkMTVkZGQwYzJmPC9zdFJlZjppbnN0YW5jZUlEPgogICAgICAgICAgICA8c3RSZWY6ZG9jdW1lbnRJRD4yOENCRkYyN0E4NjYwODU2NTkzQkU2MTVCQzVEMTREQzwvc3RSZWY6ZG9jdW1lbnRJRD4KICAgICAgICAgICAgPHN0UmVmOm9yaWdpbmFsRG9jdW1lbnRJRD4yOENCRkYyN0E4NjYwODU2NTkzQkU2MTVCQzVEMTREQzwvc3RSZWY6b3JpZ2luYWxEb2N1bWVudElEPgogICAgICAgICA8L3htcE1NOkRlcml2ZWRGcm9tPgogICAgICAgICA8ZGM6Zm9ybWF0PmltYWdlL3BuZzwvZGM6Zm9ybWF0PgogICAgICAgICA8cGhvdG9zaG9wOkNvbG9yTW9kZT4zPC9waG90b3Nob3A6Q29sb3JNb2RlPgogICAgICAgICA8cGhvdG9zaG9wOklDQ1Byb2ZpbGU+c1JHQiBJRUM2MTk2Ni0yLjE8L3Bob3Rvc2hvcDpJQ0NQcm9maWxlPgogICAgICAgICA8eG1wOkNyZWF0ZURhdGU+MjAxNS0wMS0yNlQxNzozMTo0MyswMjowMDwveG1wOkNyZWF0ZURhdGU+CiAgICAgICAgIDx4bXA6TW9kaWZ5RGF0ZT4yMDE2LTAyLTE2VDExOjQ3OjE3KzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KICAgICAgICAgPHhtcDpNZXRhZGF0YURhdGU+MjAxNi0wMi0xNlQxMTo0NzoxNyswMjowMDwveG1wOk1ldGFkYXRhRGF0ZT4KICAgICAgICAgPHhtcDpDcmVhdG9yVG9vbD5BZG9iZSBQaG90b3Nob3AgQ0MgKFdpbmRvd3MpPC94bXA6Q3JlYXRvclRvb2w+CiAgICAgICAgIDx0aWZmOkltYWdlV2lkdGg+MTA8L3RpZmY6SW1hZ2VXaWR0aD4KICAgICAgICAgPHRpZmY6SW1hZ2VMZW5ndGg+MTA8L3RpZmY6SW1hZ2VMZW5ndGg+CiAgICAgICAgIDx0aWZmOkJpdHNQZXJTYW1wbGU+CiAgICAgICAgICAgIDxyZGY6U2VxPgogICAgICAgICAgICAgICA8cmRmOmxpPjg8L3JkZjpsaT4KICAgICAgICAgICAgICAgPHJkZjpsaT44PC9yZGY6bGk+CiAgICAgICAgICAgICAgIDxyZGY6bGk+ODwvcmRmOmxpPgogICAgICAgICAgICA8L3JkZjpTZXE+CiAgICAgICAgIDwvdGlmZjpCaXRzUGVyU2FtcGxlPgogICAgICAgICA8dGlmZjpQaG90b21ldHJpY0ludGVycHJldGF0aW9uPjI8L3RpZmY6UGhvdG9tZXRyaWNJbnRlcnByZXRhdGlvbj4KICAgICAgICAgPHRpZmY6T3JpZW50YXRpb24+MTwvdGlmZjpPcmllbnRhdGlvbj4KICAgICAgICAgPHRpZmY6U2FtcGxlc1BlclBpeGVsPjM8L3RpZmY6U2FtcGxlc1BlclBpeGVsPgogICAgICAgICA8dGlmZjpYUmVzb2x1dGlvbj43MjAwMDAvMTAwMDA8L3RpZmY6WFJlc29sdXRpb24+CiAgICAgICAgIDx0aWZmOllSZXNvbHV0aW9uPjcyMDAwMC8xMDAwMDwvdGlmZjpZUmVzb2x1dGlvbj4KICAgICAgICAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICAgICAgICAgPGV4aWY6RXhpZlZlcnNpb24+MDIyMTwvZXhpZjpFeGlmVmVyc2lvbj4KICAgICAgICAgPGV4aWY6Q29sb3JTcGFjZT4xPC9leGlmOkNvbG9yU3BhY2U+CiAgICAgICAgIDxleGlmOlBpeGVsWERpbWVuc2lvbj4xMDwvZXhpZjpQaXhlbFhEaW1lbnNpb24+CiAgICAgICAgIDxleGlmOlBpeGVsWURpbWVuc2lvbj4xMDwvZXhpZjpQaXhlbFlEaW1lbnNpb24+CiAgICAgIDwvcmRmOkRlc2NyaXB0aW9uPgogICA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSJ3Ij8+KnMd9gAAACBjSFJNAAB6JQAAgIMAAPn/AACA6QAAdTAAAOpgAAA6mAAAF2+SX8VGAAAAsklEQVR42iyPQUrEUBAF672fjgEJev/r6caNIDJjzE93uxhrXVCUvt7fIvS0RyUSXWjQhcR1pp9fVyBnSXQDD6kRI4Yx6x7nkdeZmYUARqiullmATtYtzvu0IRqGzAhV4r6QkNleIpMHneRsGnd3JTKCbY951DwuL0hCWBZ0XdRFF9seknKSVTL2QBLgBShghH9v8/Y5EZrfH5m1rBb/Y4DM/EnAEsPupBsJmkcI67zn3wCswman56iUqgAAAABJRU5ErkJggg==');">
				<div id="div_form_page" align="center">
					<form id="form1" method="post" enctype="multipart/form-data" action="">
						<div style="width: 90%; font-size: 12px;">
								<div style="height: 40px;"></div>
								<div class="style4" style="height: 20px; border-bottom: solid 2px red; font-size: 15px;">
									<b>ULAŞIM ve KONAKLAMA BAŞVURU FORMU</b>
								</div>
								<div style="height: 10px;"></div>
								<div style="display: flex;" style="height: 15px;">
									<div align="left" style="width: 50%;">
										<div id="div_route" hidden>
											<b>Seyahat Rotası: </b><font id="fnt_route"></font>
										</div>
									</div>
									<div align="right" style="width: 50%;">
										<div id="div_request_data">
											<b>Talep Tarihi: </b><?php echo date("d.m.Y"); ?>
										</div>
									</div>
								</div>
								<div style="height: 10px;"></div>
								<div align="left" class="gray_frame" style="height: 13px; background-color: #CDCDCD;">
									<b>&nbsp;Talep Sahibi Bilgileri</b>
								</div>
								<div class="gray_frame">
									<div style="height: 10px;"></div>
									<div style="display: flex;">
										<div style="width: 3%;"></div>
										<div style="width: 94%;">
											<div id="div_traveler_type">
												<div style="display: flex;">
													<div align="left" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div id="div_request_owner">
																<div style="display: flex; height: 38px;">
																	<div style="width: 35%;">
																		<div id="div_lbl0" class="lbl_norm1" alert="lbl_alrt1">Talep Sahibi: <font style="color: red;">*</font></div>
																	</div>
																	<div style="width: 65%;">
																		<input type="hidden" id="inp_requester_type" name="inp_sel_requester_type" />
																		<select id="sel_requester_type" name="sel_requester_type" label="div_lbl0" area="area1" action="go" class="inp" onChange="set_requester_type($(this));">
																			<option value="">-- Seçiniz --</option>
																			<option value="1">Kendim</option>
																			<option value="2">Personel</option>
																			<option value="3">Misafir</option>
																		</select>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div align="right" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div id="div_travel_route">
																<div style="display: flex; height: 38px;" on>
																	<div style="width: 35%;">
																		<div id="div_lbl1" class="lbl_norm1" alert="lbl_alrt1">Seyahat Rotası: <font style="color: red;">*</font></div>
																	</div>
																	<div id="div_frame_route" class="radio_frame" style="width: 65%; margin-left: 1px;">
																		<div id="div_radio_group_route" class="radio_group">
																			<input type="hidden" id="inp_route" name="inp_route" value="" />
																			<input type="hidden" id="inp_route_name" name="inp_route_name" value="" />
																			<div style="width: 5px;"></div>
																			<div style="width: 24px;">
																				<input type="radio" id="rb_route1" name="rb_route" label="div_lbl1" area="area1" action="go" value="1" class="style5" onChange="set_travel_route($(this));" />
																			</div>
																			<label for="rb_route1" style="margin-top: 2px;">Yurt İçi</label>
																			<div style="width: 20px;"></div>
																			<div style="width: 24px;">
																				<input type="radio" id="rb_route2" name="rb_route" label="div_lbl1" area="area1" action="go" value="2" class="style5" onChange="set_travel_route($(this));" />
																			</div>
																			<label for="rb_route2" style="margin-top: 2px;">Yurt Dışı</label>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="div_personal_info" hidden>
												<div><input type="hidden" id="inp_uuid" name="inp_uuid" value="" /></div>
												<div id="div_blank_line1" class="gray_line"></div>
												<div style="display: flex;">
													<div align="left" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl2" class="lbl_norm1" alert="lbl_alrt1">Kimlik No: <font style="color: red;">*</font></div>
																</div>
																<div style="width: 65%;">
																	<input type="text" id="inp_identityno" name="inp_identityno" label="div_lbl2" area="area1" action="go" class="inp" />
																</div>
															</div>
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl3" class="lbl_norm1" alert="lbl_alrt1">Adı Soyadı: <font style="color: red;">*</font></b></div>
																</div>
																<div style="display: flex; width: 65%;">
																	<input type="text" id="inp_name" name="inp_name" label="div_lbl3" area="area1" action="go" class="inp" style="width: 49%;" readonly />
																	<div style="width: 2%;"></div>
																	<input type="text" id="inp_surname" name="inp_surname" label="div_lbl3" area="area1" action="go" class="inp" style="width: 49%;" readonly />
																</div>
															</div>
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl4" class="lbl_norm1" alert="lbl_alrt1">Doğum Tarihi: <font style="color: red;">*</font></div>
																</div>
																<div style="width: 65%;">
																	<input type="date" id="inp_birthdate" name="inp_birthdate" label="div_lbl4" area="area1" action="go" class="inp" />
																</div>
															</div>
														</div>
													</div>
													<div align="right" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div id="div_passport" hidden>
																<div style="display: flex; height: 38px;">
																	<div style="width: 35%;">
																		<div id="div_lbl5" class="lbl_norm1" alert="lbl_alrt1">Pasaport No: <font style="color: red;">*</font></div>
																	</div>
																	<div style="width: 65%;">
																		<input type="text" id="inp_passportno" name="inp_passportno" label="div_lbl5" area="area1" action="go" class="inp" />
																	</div>
																</div>
															</div>
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl6" class="lbl_norm1" alert="lbl_alrt1">Telefon No: <font style="color: red;">*</font></div>
																</div>
																<div style="width: 65%;">
																	<input type="text" id="inp_phone" name="inp_phone" label="div_lbl6" area="area1" action="go" class="inp" />
																</div>
															</div>
															<div style="display: flex; height: 38px;">
																<div style="width: 35%">
																	<div id="div_lbl7" class="lbl_norm1" alert="lbl_alrt1">Mail Adresi: <font style="color: red;">*</font></div>
																</div>
																<div style="width: 65%;">
																	<input type="text" id="inp_mail" name="inp_mail" label="div_lbl7" area="area1" action="go" class="inp" readonly />
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="div_staff_info" hidden>
												<div id="div_blank_line2" class="gray_line"></div>
												<div style="display: flex;">
													<div align="left" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl8" class="lbl_norm1" alert="lbl_alrt1">Görevi / Ünvanı:</div>
																</div>
																<div style="width: 65%;">
																	<div hidden><input type="text" id="inp_position" name="inp_position" label="div_lbl8" area="area1" action="go" class="inp" readonly /></div>
																	<div hidden><select id="sel_position" name="sel_position" label="div_lbl8" area="area1" action="go" class="inp"></select></div>
																</div>
															</div>
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl9" class="lbl_norm1" alert="lbl_alrt1">Departmanı:</div>
																</div>
																<div style="width: 65%;">
																	<div hidden><input type="text" id="inp_department" name="inp_department" label="div_lbl9" area="area1" action="go" class="inp" readonly /></div>
																	<div hidden><select id="sel_department" name="sel_department" label="div_lbl9" area="area1" action="go" class="inp"></select></div>
																</div>
															</div>
														</div>
													</div>
													<div align="right" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div style="display: flex; height: 38px;">
																<div style="width: 35%;">
																	<div id="div_lbl10" class="lbl_norm1" alert="lbl_alrt1">Çalışma Şubesi:</div>
																</div>
																<div style="width: 65%;">
																	<div hidden><input type="text" id="inp_location" name="inp_location" label="div_lbl10" area="area1" action="go" class="inp" readonly /></div>
																	<div hidden><select id="sel_location" name="sel_location" label="div_lbl10" area="area1" action="go" class="inp"></select></div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="div_add_button" hidden>
												<div id="div_blank_line3" class="gray_line"></div>
												<div style="display: flex;">
													<div align="left" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div id="div_add">
																<div style="display: flex; height: 38px;">
																	<div style="width: 100%;">
																		<input type="button" id="btn_add_traveler" area="area1" action="warn" class="btn" value="Ekle ✚" onClick="add_traveler($(this));" />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div id="div_traveler_list" hidden>
												<div align="left" style="height: 13px; border: solid 2px #B5D4EB; background-color: #B5D4EB;">
													<b>&nbsp;Seyahat Edecekler</b>
												</div>
												<div class="trv_lst">
													<div id="div_traveler_line_frame1" class="trv_lst_frm1">
														<table class="trv_tbl1">
															<thead>
																<tr class="trv_thead">
																	<th hidden></th>
																	<th class="trv_cell_th">Adı</th>
																	<th class="trv_cell_th">Soyadı</th>
																	<th class="trv_cell_th">Doğum Tarihi</th>
																	<th class="trv_cell_th">Kimlik No</th>
																	<th id="th_trv_list_passport" class="trv_cell_th" hidden>Pasaport No</th>
																	<th class="trv_cell_th">Telefon No</th>
																	<th class="trv_cell_th">Mail Adresi</th>
																	<th class="trv_cell_th">Görevi / Ünvanı</th>
																	<th hidden></th>
																	<th class="trv_cell_th">Departmanı</th>
																	<th hidden></th>
																	<th class="trv_cell_th">Çalışma Şubesi</th>
																	<th hidden></th>
																</tr>
															</thead>
															<tbody id="tb_traveler_list"></tbody>
														</table>
													</div>
													<div id="div_traveler_line_frame2" class="trv_lst_frm2">
														<table class="trv_tbl2">
															<thead>
																<tr class="trv_thead">
																	<th colspan="2" class="trv_cell_th">İşlem</th>
																</tr>
															</thead>
															<tbody id="tb_process_list"></tbody>
														</table>
													</div>
												</div>
												<div style="height: 10px;"></div>
												<div style="display: flex;">
													<div align="left" style="width: 50%;">
														<div align="left" style="width: 97%;">
															<div id="div_continue">
																<div style="display: flex; height: 38px;">
																	<div style="width: 100%;">
																		<input type="button" id="btn_continue" area="area2" action="go" class="btn" value="Devam ⮞" onClick="set_travel_info($(this));" />
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div style="width: 3%;"></div>
									</div>
									<div style="height: 10px;"></div>
								</div>
								<div style="height: 30px;"></div>
								<div align="left" class="blue_line">
									<b>Seyahat Bilgileri</b>
								</div>
								<div style="height: 20px;"></div>
								<div id="div_travel_info" hidden>
									<div style="display: flex;">
										<div align="left" style="width: 50%;">
											<div id="div_from_country" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl11" class="lbl_norm2" alert="lbl_alrt2">Nereden: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_from_country" name="inp_from_country" />
													<select id="sel_from_country" name="sel_from_country" label="div_lbl11" area="area2" action="go" class="inp" onChange="set_country($(this), $('#div_from_location'), $('#sel_from_location'), $('#div_from_city'), $('#inp_from_city'), $('#sel_from_city'));"></select>
												</div>
											</div>
											<div id="div_from_location" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl12" class="lbl_norm2" alert="lbl_alrt2">Lokasyon: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_from_location" name="inp_from_location" />
													<select id="sel_from_location" name="sel_from_location" label="div_lbl12" area="area2" action="go" class="inp" onChange="set_location($(this), $('#sel_from_country'), $('#div_from_city'), $('#inp_from_city'), $('#sel_from_city'));"></select>
												</div>
											</div>
											<div id="div_from_city" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl13" class="lbl_norm2" alert="lbl_alrt2">Şehir: <font style="color: red;">*</font></div>
												<div>
													<div hidden><input type="text" id="inp_from_city" name="inp_from_city" label="div_lbl13" area="area2" action="go" class="inp" /></div>
													<div hidden><select id="sel_from_city" name="sel_from_city" label="div_lbl13" area="area2" action="go" class="inp" onChange="set_city();"></select></div>
												</div>
											</div>
										</div>
										<div align="right" style="width: 50%;">
											<div id="div_to_country" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl14" class="lbl_norm2" alert="lbl_alrt2">Nereye: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_to_country" name="inp_to_country" />
													<select id="sel_to_country" name="sel_to_country" label="div_lbl14" area="area2" action="go" class="inp" onChange="set_country($(this), $('#div_to_location'), $('#sel_to_location'), $('#div_to_city'), $('#inp_to_city'), $('#sel_to_city'));"></select>
												</div>
											</div>
											<div id="div_to_location" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl15" class="lbl_norm2" alert="lbl_alrt2">Lokasyon: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_to_location" name="inp_to_location" />
													<select id="sel_to_location" name="sel_to_location" label="div_lbl15" area="area2" action="go" class="inp" onChange="set_location($(this), $('#sel_to_country'), $('#div_to_city'), $('#inp_to_city'), $('#sel_to_city'));"></select>
												</div>
											</div>
											<div id="div_to_city" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl16" class="lbl_norm2" alert="lbl_alrt2">Şehir: <font style="color: red;">*</font></div>
												<div>
													<div hidden><input type="text" id="inp_to_city" name="inp_to_city" label="div_lbl16" area="area2" action="go" class="inp" /></div>
													<div hidden><select id="sel_to_city" name="sel_to_city" label="div_lbl16" area="area2" action="go" class="inp" onChange="set_city();"></select></div>
												</div>
											</div>
										</div>
									</div>
									<div style="display: flex;">
										<div align="left" style="width: 50%;">
											<div id="div_travel_reason" align="left" style="width: 97%; height: 58px;" hidden>
												<div id="div_lbl17" class="lbl_norm2" alert="lbl_alrt2">Seyahat Nedeni: <font style="color: red;">*</font></div>
												<div>
													<input type="hidden" id="inp_travel_reason" name="inp_travel_reason" />
													<select id="sel_travel_reason" name="sel_travel_reason" label="div_lbl17" area="area2" action="go" class="inp" onChange="toggle_visibility([$('#div_transportation_accommodation')], []);"></select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div style="height: 20px;"></div>
								<div id="div_lbl18" align="left" class="blue_line">
									<b>Ulaşım ve Konaklama Bilgileri</b>
								</div>
								<div style="height: 10px;"></div>
								<div id="div_transportation_accommodation" align="center" hidden>
									<div id="div_frame_trac" class="radio_frame">
										<div id="div_radio_group_trac" align="left" class="radio_group">
											<div style="width: 5px;"></div>
											<div style="width: 24px;">
												<input type="radio" id="rb_trac1" name="rb_trac" label="div_lbl18" area="area2" action="go" value="1" class="style5" onChange="toggle_visibility([$('#div_transportation'), $('#div_accommodation'), $('#div_save')], []);" />
											</div>
											<label for="rb_trac1" style="margin-top: 2px;">Ulaşım ve Konaklama Talebi</label>
											<div style="width: 20px;"></div>
											<div style="width: 24px;">
												<input type="radio" id="rb_trac2" name="rb_trac" label="div_lbl18" area="area2" action="go" value="2" class="style5" onChange="toggle_visibility([$('#div_transportation'), $('#div_save')], [$('#div_accommodation')]);" />
											</div>
											<label for="rb_trac2" style="margin-top: 2px;">Ulaşım Talebi</label>
											<div style="width: 20px;"></div>
											<div style="width: 24px;">
												<input type="radio" id="rb_trac3" name="rb_trac" label="div_lbl18" area="area2" action="go" value="3" class="style5" onChange="toggle_visibility([$('#div_accommodation'), $('#div_save')], [$('#div_transportation')]);" />
											</div>
											<label for="rb_trac3" style="margin-top: 2px;">Konaklama Talebi</label>
											<div style="width: 20px;"></div>
											<div><font style="color: red; font-weight: bold;">*</font></div>
										</div>
									</div>
								</div>
								<div style="height: 20px;"></div>
								<div id="div_transportation" hidden>
									<div align="left" class="blue_line">
										<b>Ulaşım Bilgileri</b>
									</div>
									<div style="height: 20px;"></div>
									<div style="display: flex;">
										<div align="left" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div style="height: 58px;">
													<div id="div_lbl19" class="lbl_norm2" alert="lbl_alrt2">Gidiş Tarihi: <font style="color: red;">*</font></div>
													<div><input type="date" id="inp_departure_date" name="inp_departure_date" label="div_lbl19" area="area2" action="go" class="inp" /></div>
												</div>
												<div style="height: 58px;">
													<div id="div_lbl20" class="lbl_norm2" alert="lbl_alrt2">Dönüş Tarihi:</div>
													<div><input type="date" id="inp_return_date" name="inp_return_date" label="div_lbl20" area="area2" action="go" class="inp" /></div>
												</div>
												<div style="height: 58px;">
													<div id="div_lbl21" class="lbl_norm2" alert="lbl_alrt2">Transfer İhtiyaç Durumu: <font style="color: red;">*</font></div>
													<div id="div_frame_tns" class="radio_frame">
														<div id="div_radio_group_tns" class="radio_group">
															<input type="hidden" id="inp_transfer_need_situation" name="inp_transfer_need_situation" />
															<input type="hidden" id="inp_transfer_need_situation_name" name="inp_transfer_need_situation_name" />
															<div style="width: 5px;"></div>
															<div style="width: 24px;">
																<input type="radio" id="rb_tns1" name="rb_tns" label="div_lbl21" area="area2" action="go" value="1" class="style5" onClick="set_transfer_need_situation($(this));" />
															</div>
															<label for="rb_tns1" style="margin-top: 2px;">Var</label>
															<div style="width: 20px;"></div>
															<div style="width: 24px;">
																<input type="radio" id="rb_tns2" name="rb_tns" label="div_lbl21" area="area2" action="go" value="2" class="style5" onClick="set_transfer_need_situation($(this));" />
															</div>
															<label for="rb_tns2" style="margin-top: 2px;">Yok</label>
														</div>
													</div>
												</div>
												<div id="div_nftd" style="height: 58px;" hidden>
													<div id="div_lbl22" class="lbl_norm2" alert="lbl_alrt2">Transfer İhtiyaç Detayı: <font style="color: red;">*</font></div>
													<div><input type="text" id="inp_transfer_need_detail" name="inp_transfer_need_detail" label="div_lbl22" area="area2" action="go" class="inp" /></div>
												</div>
											</div>
										</div>
										<div align="right" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div style="height: 58px;">
													<div id="div_lbl23" class="lbl_norm2" alert="lbl_alrt2">Ulaşım Yöntemi: <font style="color: red;">*</font></div>
													<div>
														<input type="hidden" id="inp_transportation_mode" name="inp_transportation_mode" />
														<select id="sel_transportation_mode" name="sel_transportation_mode" label="div_lbl23" area="area2" action="go" class="inp"></select>
													</div>
												</div>
												<div style="height: 116px;">
													<div id="div_lbl24" class="lbl_norm2" alert="lbl_alrt2">Ulaşım Detayları: <font style="color: red;"></font></div>
													<div><textarea id="txt_transportation_detail" name="txt_transportation_detail" label="div_lbl24" area="area2" action="go" class="inp" style="height: 88px;"></textarea></div>
												</div>
											</div>
										</div>
									</div>
									<div style="height: 10px;"></div>
								</div>
								<div id="div_accommodation" hidden>
									<div align="left" class="blue_line">
										<b>Konaklama Bilgileri</b>
									</div>
									<div style="height: 20px;"></div>
									<div style="display: flex;">
										<div align="left" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div style="height: 58px;">
													<div id="div_lbl25" class="lbl_norm2" alert="lbl_alrt2">Konaklama Başlangıç Tarihi: <font style="color: red;">*</font></div>
													<div><input type="date" id="inp_check-in_date" name="inp_check-in_date" label="div_lbl25" area="area2" action="go" class="inp" /></div>
												</div>
												<div style="height: 58px;">
													<div id="div_lbl26" class="lbl_norm2" alert="lbl_alrt2">Konaklama Bitiş Tarihi:</div>
													<div><input type="date" id="inp_check-out_date" name="inp_check-out_date" label="div_lbl26" area="area2" action="go" class="inp" /></div>
												</div>
											</div>
										</div>
										<div align="right" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div style="height: 110px;">
													<div id="div_lbl27" class="lbl_norm2" alert="lbl_alrt2">Konaklama Detayları: <font style="color: red;"></font></div>
													<div><textarea id="txt_accommodation_detail" name="txt_accommodation_detail" label="div_lbl27" area="area2" action="go" class="inp" style="height: 88px;"></textarea></div>
												</div>
											</div>
										</div>
									</div>
									<div style="height: 10px;"></div>
								</div>
								<div id="div_save" hidden>
									<div class="subheading" style="height: 2px;"></div>
									<div style="height: 10px;"></div>
									<div style="display: flex; height: 38px;">
										<div align="right" style="width: 100%;">
											<input type="button" id="btn_add_request" area="area2" action="warn" class="btn" value="Kaydet ⮟" onClick="add_request($(this));" />
										</div>
									</div>
								</div>
								<div style="height: 22px;"></div>
							</div>
					</form>
					<script type="text/javascript">
						// fill_selection_list($('#sel_requester_type'), 'user', 'CONCAT(name, " ", surname)');
						fill_selection_list($('#sel_location'), 'location');
						fill_selection_list($('#sel_position'), 'position');
						fill_selection_list($('#sel_department'), 'department');
						fill_selection_list($('#sel_from_country'), 'country');
						// fill_selection_list($('#sel_from_location'), 'location');
						fill_selection_list($('#sel_from_city'), 'city', '', 'COUNTRY_ID = \'220\'');
						fill_selection_list($('#sel_travel_reason'), 'reason');
						fill_selection_list($('#sel_transportation_mode'), 'transportation_mode');
						$('#sel_requester_type').focus();
					</script>		
				</div>
				<div id="div_approve_page" align="center" hidden></div>
			</div>
		</div>
	</body>
</html>