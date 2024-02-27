<?php
	require("check_session.php");

	unset($_SESSION['request']);
?>
<html xmlns="http://www.w3.org/2001/XMLSchema">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=750">

		<title>Ulaşım ve Konaklama Başvuru Formu</title>

		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

		<script src="./js/jquery-3.7.1.js"></script>
		<script src="./js/jquery-ui-1.13.2/jquery-ui.js"></script>

		<script src="./js/jquery.inputmask.min.js"></script>
		<script src="./js/inputmask.binding.js"></script>

		<script type="text/javascript">
			var xmlFile = "./alert.xml";
			var xmlData = [];

			$.ajax({
				type: "GET",
				url: xmlFile,
				dataType: "xml",
				success: function(xml) {
					$(xml).find('form').children().each(function() {
						var sectionData = [];
						$(this).children().each(function() {
							var inputData = [];
							$(this).children().each(function() {
								inputData[this.tagName] = $(this).text();
							});
							sectionData.push(inputData);
						});
						xmlData[this.tagName] = sectionData;
					});
				},
				error: function(xhr, status, error) {
					alert("XML dosyası okunamadı: " + error);
				}
			});

			$(document).ready(function() {
				//$('#inp_birthdate').inputmask("gg.aa.yyyy");
				$('#inp_identityno').inputmask("99999999999");
				$('#inp_phone').inputmask("9(999) 999 99 99");

				$(document).on("mousedown", function(event) {
					rgfocus(event);
				});

				$(':input').on("focus", function(event) {
					rgfocus(event);
				});

				$(':input').on("blur", function(event) {
					if(!$(event.target).attr('readonly')) {
						if(($(event.target).attr('id') == 'inp_name' || $(event.target).attr('id') == 'inp_surname' || $(event.target).attr('id') == 'inp_birthdate') &&
							$('#inp_name').val() != '' && $('#inp_surname').val() != '' && $('#inp_birthdate').val() != '') {
							get_traveler_info('name', 'name=' + $('#inp_name').val() + '&surname=' + $('#inp_surname').val() + '&birthdate=' + $('#inp_birthdate').val());
						} else if($(event.target).attr('id') == 'inp_identityno' && $(event.target).val() != '') {
							get_traveler_info('identityno', 'identityno=' + $(event.target).val());
						} else if($(event.target).attr('id') == 'inp_passportno' && $(event.target).val() != '') {
							get_traveler_info('passportno', 'passportno=' + $(event.target).val());
						} else if($(event.target).attr('id') == 'inp_mail' && $(event.target).val() != '') {
							get_traveler_info('mail', 'mail=' + $(event.target).val());
						}
					}
				});

				$(':input').on("keydown", function(event) {
					if(event.target.tagName != 'textarea' && $(event.target).attr('type') != 'button' && event.which == '13') {
						check_form_data($(event.target).attr('area'), 'go');
					}
				});

				$("#inp_name").on("input", function() {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$("#inp_surname").on("input", function() {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$("#inp_mail").on("input", function() {
					var value = $(this).val().replace(/ç/g, "c").replace(/Ç/g, "c")
											 .replace(/ğ/g, "g").replace(/Ğ/g, "g")
											 .replace(/İ/g, "i").replace(/ı/g, "i")
											 .replace(/ö/g, "o").replace(/Ö/g, "o")
											 .replace(/ş/g, "s").replace(/Ş/g, "s")
											 .replace(/ü/g, "u").replace(/Ü/g, "u")
											 .toLowerCase();
					$(this).val(value);
				});

				$("#inp_from_city").on("input", function() {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_from_city').on("keyup", function(event) {
					set_reason();
				});

				$("#inp_to_city").on("input", function() {
					var value = $(this).val().replace(/ı/g, "I").replace(/i/g, "İ").toUpperCase();
					$(this).val(value);
				});

				$('#inp_to_city').on("keyup", function(event) {
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
								set_traveler_information(rowData);
							} else if(search_type == 'mail') {
								$.getJSON('./get_traveler_info_from_ad.php?' + postdata,
									function(data) {
										if(data.Rows) {
											var rowData = data.Rows[0];
											if(rowData.status == 1) {
												set_traveler_information(rowData);
											}
										}
									}
								)
							}
						}
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

			function search_form_data(section, action) {
				var result = true;
				$.each(xmlData[section], function() {
					if(eval(this['condition'])) {
						if(action == 'warn') {
							alert(this['alert']);
							$('#' + this['label']).attr('class', this['class']);
						} else if(this['forced_alert'] == '1') {
							alert(this['alert']);
						}
						$('#' + this['focus']).focus();
						result = false;
						return false;
					}
				});
				return result;
			}

			function check_form_data(area, action) {
				reset_alerts();
				
				if(area == 'area1') {
					if(search_form_data('section1', action)) {
						$('#inp_add').focus();
						return true;
					} else {
						return false;
					}
				} else if(area == 'area2') {
					if($('#rb_route1').prop('checked')) {
						if(!search_form_data('section2', action)) {
							return false;
						}
					} else if($('#rb_route2').prop('checked')) {
						if(!search_form_data('section3', action)) {
							return false;
						}
					}
					if(!search_form_data('section4', action)) {
						return false;
					}
					if(!search_form_data('section5', action)) {
						return false;
					}
					if($('#rb_trac1').prop('checked') || $('#rb_trac2').prop('checked')) {
						if(!search_form_data('section6', action)) {
							return false;
						}
					}
					if($('#rb_trac1').prop('checked') || $('#rb_trac3').prop('checked')) {
						if(!search_form_data('section7', action)) {
							return false;
						}
					}
					$('#inp_save').focus();
					return true;
				}
			}

			function set_selected_text(item) {
				$('#inp_' + item).val($('#sel_' + item + ' option:selected').text());
			}

			function set_traveler_information(data) {
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

				var focus_item = $('#sel_requester_type');

				if(item.val() == '') {
					toggle_visibility([], [$('#div_personal_info'), $('#div_staff_info'), $('#div_add_button')]);
				} else if(item.val() == '1') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent(), $('#div_add_button')], [$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()]);
					toggle_writability([], [$('#inp_name'), $('#inp_surname'), $('#inp_mail')]);

					$.getJSON('./get_user_information.php',
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								set_traveler_information(rowData);
							}
						}
					);

					focus_item = $('#inp_birthdate');
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent(), $('#div_add_button')], [$('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent()]);
					toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_mail')], []);
					focus_item = $('#inp_name');
/*
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent(), $('#div_add_button')], [$('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent()]);
					toggle_writability([$('#inp_mail')], [$('#inp_name'), $('#inp_surname')]);
					focus_item = $('#inp_mail');
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_personal_info'), $('#div_staff_info'), $('#sel_position').parent(), $('#sel_department').parent(), $('#sel_location').parent(), $('#div_add_button')], [$('#inp_position').parent(), $('#inp_department').parent(), $('#inp_location').parent()]);
					toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_mail')], []);
					focus_item = $('#inp_name');
*/
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_personal_info'), $('#div_add_button')], [$('#div_staff_info')]);
					toggle_writability([$('#inp_name'), $('#inp_surname'), $('#inp_mail')], []);
					focus_item = $('#inp_name');
				}

				if($('#inp_route').val() == '') {
					$('#rb_route1').focus();
				} else {
					focus_item.focus();
				}
			}

			function set_travel_route(item) {
				reset_values([$('#sel_from_country'), $('#sel_to_country'), $('#sel_from_location'), $('#sel_to_location'), $('#inp_from_city'), $('#inp_to_city'), $('#sel_from_city'), $('#sel_to_city')]);

				$('#sel_to_country').html($('#sel_from_country').html());
				$('#sel_to_city').html($('#sel_from_city').html());

				if(item.attr('id') == 'rb_route1') {
					$('#inp_route').val('1');
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
					$('#fnt_route').html('Yurt Dışı');
					toggle_visibility([$('#div_passport')], []);
					$('#th_trv_list_passport').show();
					$('#div_lbl12').html('Lokasyon: <font style="color: red;">*</font>');
					$('#div_lbl15').html('Lokasyon: <font style="color: red;">*</font>');
					toggle_visibility([$('#div_from_country'), $('#div_to_country')], [$('#div_from_location'), $('#div_to_location'), $('#div_from_city'), $('#div_to_city')]);
				}
			}

			function add_traveler() {
				if($('#inp_add').val() == 'Güncelle ⭮') {
					$('#inp_add').val('Ekle ✚');
					$('#sel_requester_type').prop('disabled', false);
				}

				if(!check_form_data('area1', 'warn')) {
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

								alert('Seçili kişiye ait bilgiler güncellendi.');

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
						$('#inp_continue').focus();

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

				$('#inp_add').val('Güncelle ⭮');
			}

			function del_traveler(uuid) {
				if(confirm(uuid + ' numaralı kaydı silmek istiyor musunuz?')) {
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

			function set_travel_info() {
				if($('#inp_continue').val() == 'Devam ⮞') {
					$('#inp_continue').val('Geri ⮝');
					toggle_visibility([$('#div_route'), $('#div_travel_info')], [$('#div_traveler_type'), $('#div_personal_info'), $('#div_staff_info'), $('#div_add_button')]);
					check_form_data('area2', 'go');
				} else if($('#inp_continue').val() == 'Geri ⮝') {
					$('#inp_continue').val('Devam ⮞');
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
						//fill_selection_list(select_item, 'city');
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

			function set_city(item) {
				$('#inp_' + item).val($('#sel_' + item + ' option:selected').text());
				set_reason();
			}

			function set_reason() {
				if( ($('#inp_from_city').val() != '' || ($('#sel_from_city').val() !== null ? $('#sel_from_city').val() : '' != '')) &&
					($('#inp_to_city').val() != '' || ($('#sel_to_city').val() !== null ? $('#sel_to_city').val() : '' != '')) ) {
					$('#div_travel_reason').show();
				}
			}

			function save_request() {
				if(!check_form_data('area2', 'warn')) {
					return false;
				}

				var formData = $('#form1').serialize();

				$.getJSON('./add_request.php?' + formData).done(
					function() {
						alert('İlk aşama başarı ile geçildi.');
						$.getJSON('./save_request.php',
							function(data) {
								if(data.Rows) {
									var rowData = data.Rows[0];
									
									alert(rowData.status + ' Buraya kadar sorun yok.');
								}
							}
						);
					}
				);
			}

		</script>
	</head>
	<body background="./images/body_gray.png" topmargin="20">
		<center>
			<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="">
				<div align="center" class="style4" style="width: 900px; border: groove; background: url('./images/body.png');">
					<div style="width: 90%; font-size: 12px;">
						<div style="height: 40px;"></div>
						<div class="style4" style="height: 20px; border-bottom: solid 2px red; font-size: 15px;">
							<b>ULAŞIM ve KONAKLAMA BAŞVURU FORMU</b>
						</div>
						<div style="height: 10px;"></div>
						<div style="display: flex;" style="height: 15px;">
							<div align="left" style="width: 50%;">
								<div id="div_route" hidden>
									<input type="hidden" id="inp_route" name="inp_route" value="" /><b>Seyahat Rotası: </b><font id="fnt_route"></font>
								</div>
							</div>
							<div align="right" style="width: 50%;">
								<div id="div_request_data">
									<input type="hidden" id="inp_request_date" name="inp_request_date" value="<?php echo date("d.m.Y"); ?>" /><b>Talep Tarihi: </b><?php echo date("d.m.Y"); ?>
								</div>
							</div>
						</div>
						<div style="height: 10px;"></div>
						<div align="left" style="height: 13px; border: solid 2px #CDCDCD; background-color: #CDCDCD;">
							<b>&nbsp;Talep Sahibi Bilgileri</b>
						</div>
						<div style="border: solid 2px #CDCDCD;">
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
																<div id="div_lbl0" class="lbl_norm1">Talep Sahibi: <font style="color: red;">*</font></div>
															</div>
															<div style="width: 65%;">
																<select id="sel_requester_type" name="sel_requester_type" area="area1" class="style5" style="width: 100%; height: 30px;" onChange="set_requester_type($(this));">
																	<option value="">-- Seçiniz --</option>
																	<option value="1">Kendim</option>
																	<option value="2">Personel</option>
																<!--	<option value="2">Personel (MLPCare)</option>
																	<option value="3">Personel (Diğer)</option>	-->
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
																<div id="div_lbl1" class="lbl_norm1">Seyahat Rotası: <font style="color: red;">*</font></div>
															</div>
															<div id="div_frame_route" class="radio_frame" style="width: 65%; height: 28px; margin-left: 1px;">
																<div id="div_radio_group_route" style="display: flex; height: 79%; padding-top: 4px; border: solid 1px transparent;">
																	<div style="width: 5px;"></div>
																	<div style="width: 24px;">
																		<input type="radio" id="rb_route1" name="rb_route" area="area1" value="1" class="style5" onChange="set_travel_route($(this));" />
																	</div>
																	<label for="rb_route1" style="margin-top: 2px;">Yurt İçi</label>
																	<div style="width: 20px;"></div>
																	<div style="width: 24px;">
																		<input type="radio" id="rb_route2" name="rb_route" area="area1" value="2" class="style5" onChange="set_travel_route($(this));" />
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
										<div id="div_blank_line1" style="height: 8px; border-top: solid 2px #CDCDCD;"></div>
										<div style="display: flex;">
											<div align="left" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl2" class="lbl_norm1">Adı Soyadı: <font style="color: red;">*</font></b></div>
														</div>
														<div style="display: flex; width: 65%;">
															<input type="text" id="inp_name" name="inp_name" area="area1" class="style5" style="width: 49%; height: 30px;" readonly />
															<div style="width: 2%;"></div>
															<input type="text" id="inp_surname" name="inp_surname" area="area1" class="style5" style="width: 49%; height: 30px;" readonly />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl3" class="lbl_norm1">Doğum Tarihi: <font style="color: red;">*</font></div>
														</div>
														<div style="width: 65%;">
															<input type="date" id="inp_birthdate" name="inp_birthdate" area="area1" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl4" class="lbl_norm1">Kimlik No: <font style="color: red;">*</font></div>
														</div>
														<div style="width: 65%;">
															<input type="text" id="inp_identityno" name="inp_identityno" area="area1" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
												</div>
											</div>
											<div align="right" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl5" class="lbl_norm1">Telefon No: <font style="color: red;">*</font></div>
														</div>
														<div style="width: 65%;">
															<input type="text" id="inp_phone" name="inp_phone" area="area1" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%">
															<div id="div_lbl6" class="lbl_norm1">Mail Adresi: <font style="color: red;">*</font></div>
														</div>
														<div style="width: 65%;">
															<input type="text" id="inp_mail" name="inp_mail" area="area1" class="style5" style="width: 100%; height: 30px;" readonly />
														</div>
													</div>
													<div id="div_passport" hidden>
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div id="div_lbl7" class="lbl_norm1">Pasaport No: <font style="color: red;">*</font></div>
															</div>
															<div style="width: 65%;">
																<input type="text" id="inp_passportno" name="inp_passportno" area="area1" class="style5" style="width: 100%; height: 30px;" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="div_staff_info" hidden>
										<div id="div_blank_line2" style="height: 8px; border-top: solid 2px #CDCDCD;"></div>
										<div style="display: flex;">
											<div align="left" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl8" class="lbl_norm1">Görevi / Ünvanı:</div>
														</div>
														<div style="width: 65%;">
															<div hidden><input type="text" id="inp_position" name="inp_position" area="area1" class="style5" style="width: 100%; height: 30px;" readonly /></div>
															<div hidden><select id="sel_position" name="sel_position" area="area1" class="style5" style="width: 100%; height: 30px;" onChange="set_selected_text('position');"></select></div>
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl9" class="lbl_norm1">Departmanı:</div>
														</div>
														<div style="width: 65%;">
															<div hidden><input type="text" id="inp_department" name="inp_department" area="area1" class="style5" style="width: 100%; height: 30px;" readonly /></div>
															<div hidden><select id="sel_department" name="sel_department" area="area1" class="style5" style="width: 100%; height: 30px;" onChange="set_selected_text('department');"></select></div>
														</div>
													</div>
												</div>
											</div>
											<div align="right" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl10" class="lbl_norm1">Çalışma Şubesi:</div>
														</div>
														<div style="width: 65%;">
															<div hidden><input type="text" id="inp_location" name="inp_location" area="area1" class="style5" style="width: 100%; height: 30px;" readonly /></div>
															<div hidden><select id="sel_location" name="sel_location" area="area1" class="style5" style="width: 100%; height: 30px;" onChange="set_selected_text('location');"></select></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="div_add_button" hidden>
										<div id="div_blank_line3" style="height: 8px; border-top: solid 2px #CDCDCD;"></div>
										<div style="display: flex;">
											<div align="left" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div id="div_add">
														<div style="display: flex; height: 38px;">
															<div style="width: 100%;">
																<input type="button" id="inp_add" class="style5" style="width: 90px; height: 30px; font-weight: bold;" value="Ekle ✚" onClick="add_traveler();" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="div_traveler_list" hidden>
										<div align="left" style="height: 13px; border: solid 2px #B5D4EB; background-color: #B5D4EB;">
											<b>&nbsp;Talep Eden / Edenler</b>
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
																<input type="button" id="inp_continue" class="style5" style="width: 90px; height: 30px; font-weight: bold;" value="Devam ⮞" onClick="set_travel_info();" />
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
						<div align="left" style="height: 17px; border-bottom: solid 2px blue;">
							<b>Seyahat Bilgileri</b>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_travel_info" hidden>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div id="div_from_country" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl11" class="lbl_norm2">Nereden: <font style="color: red;">*</font></div>
										<div>
											<select id="sel_from_country" name="sel_from_country" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_country($(this), $('#div_from_location'), $('#sel_from_location'), $('#div_from_city'), $('#inp_from_city'), $('#sel_from_city'));"></select>
										</div>
									</div>
									<div id="div_from_location" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl12" class="lbl_norm2">Lokasyon: <font style="color: red;">*</font></div>
										<div>
											<select id="sel_from_location" name="sel_from_location" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_location($(this), $('#sel_from_country'), $('#div_from_city'), $('#inp_from_city'), $('#sel_from_city'));"></select>
										</div>
									</div>
									<div id="div_from_city" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl13" class="lbl_norm2">Şehir: <font style="color: red;">*</font></div>
										<div>
											<div hidden><input type="text" id="inp_from_city" name="inp_from_city" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
											<div hidden><select id="sel_from_city" name="sel_from_city" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_city('from_city');"></select></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div id="div_to_country" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl14" class="lbl_norm2">Nereye: <font style="color: red;">*</font></div>
										<div>
											<select id="sel_to_country" name="sel_to_country" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_country($(this), $('#div_to_location'), $('#sel_to_location'), $('#div_to_city'), $('#inp_to_city'), $('#sel_to_city'));"></select>
										</div>
									</div>
									<div id="div_to_location" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl15" class="lbl_norm2">Lokasyon: <font style="color: red;">*</font></div>
										<div>
											<select id="sel_to_location" name="sel_to_location" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_location($(this), $('#sel_to_country'), $('#div_to_city'), $('#inp_to_city'), $('#sel_to_city'));"></select>
										</div>
									</div>
									<div id="div_to_city" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl16" class="lbl_norm2">Şehir: <font style="color: red;">*</font></div>
										<div>
											<div hidden><input type="text" id="inp_to_city" name="inp_to_city" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
											<div hidden><select id="sel_to_city" name="sel_to_city" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="set_city('to_city');"></select></div>
										</div>
									</div>
								</div>
							</div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div id="div_travel_reason" align="left" style="width: 97%; height: 58px;" hidden>
										<div id="div_lbl17" class="lbl_norm2">Seyahat Nedeni: <font style="color: red;">*</font></div>
										<div>
											<select id="sel_travel_reason" name="sel_travel_reason" area="area2" class="style5" style="width: 100%; height: 30px;" onChange="toggle_visibility([$('#div_transportation_accommodation')], []);"></select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_lbl18" align="left" style="height: 17px; border-bottom: solid 2px blue;">
							<b>Ulaşım ve Konaklama Bilgileri</b>
						</div>
						<div style="height: 10px;"></div>
						<div id="div_transportation_accommodation" align="center" style="height: 25px;" hidden>
							<div id="div_frame_trac" class="radio_frame" style="height: 28px;" >
								<div id="div_radio_group_trac" align="left" style="display: flex; height: 79%; padding-top: 4px; border: solid 1px transparent;">
									<div style="width: 5px;"></div>
									<div style="width: 24px;">
										<input type="radio" id="rb_trac1" name="rb_trac" area="area2" value="1" class="style5" onChange="toggle_visibility([$('#div_transportation'), $('#div_accommodation'), $('#div_save')], []);" />
									</div>
									<label for="rb_trac1" style="margin-top: 2px;">Ulaşım ve Konaklama Talebi</label>
									<div style="width: 20px;"></div>
									<div style="width: 24px;">
										<input type="radio" id="rb_trac2" name="rb_trac" area="area2" value="2" class="style5" onChange="toggle_visibility([$('#div_transportation'), $('#div_save')], [$('#div_accommodation')]);" />
									</div>
									<label for="rb_trac2" style="margin-top: 2px;">Ulaşım Talebi</label>
									<div style="width: 20px;"></div>
									<div style="width: 24px;">
										<input type="radio" id="rb_trac3" name="rb_trac" area="area2" value="3" class="style5" onChange="toggle_visibility([$('#div_accommodation'), $('#div_save')], [$('#div_transportation')]);" />
									</div>
									<label for="rb_trac3" style="margin-top: 2px;">Konaklama Talebi</label>
									<div style="width: 20px;"></div>
									<div><font style="color: red; font-weight: bold;">*</font></div>
								</div>
							</div>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_transportation" hidden>
							<div align="left" style="height: 17px; border-bottom: solid 2px blue;">
								<b>Ulaşım Bilgileri</b>
							</div>
							<div style="height: 20px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div id="div_lbl19" class="lbl_norm2">Gidiş Tarihi: <font style="color: red;">*</font></div>
											<div><input type="date" id="inp_departure_date" name="inp_departure_date" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 58px;">
											<div id="div_lbl20" class="lbl_norm2">Dönüş Tarihi:</div>
											<div><input type="date" id="inp_return_date" name="inp_return_date" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 58px;">
											<div id="div_lbl21" class="lbl_norm2">Transfer İhtiyaç Durumu: <font style="color: red;">*</font></div>
											<div id="div_frame_tns" class="radio_frame" style="height: 28px;">
												<div id="div_radio_group_tns" style="display: flex; height: 79%; padding-top: 4px; border: solid 1px transparent;">
													<div style="width: 5px;"></div>
													<div style="width: 24px;">
														<input type="radio" id="rb_tns1" name="rb_tns" area="area2" value="1" class="style5" onClick="toggle_visibility([$('#div_nftd')], []);" />
													</div>
													<label for="rb_tns1" style="margin-top: 2px;">Var</label>
													<div style="width: 20px;"></div>
													<div style="width: 24px;">
														<input type="radio" id="rb_tns2" name="rb_tns" area="area2" value="0" class="style5" onClick="toggle_visibility([], [$('#div_nftd')]);" />
													</div>
													<label for="rb_tns2" style="margin-top: 2px;">Yok</label>
												</div>
											</div>
										</div>
										<div id="div_nftd" style="height: 58px;" hidden>
											<div id="div_lbl22" class="lbl_norm2">Transfer İhtiyaç Detayı: <font style="color: red;">*</font></div>
											<div><input type="text" id="inp_transfer_need_detail" name="inp_transfer_need_detail" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div id="div_lbl23" class="lbl_norm2">Ulaşım Yöntemi: <font style="color: red;">*</font></div>
											<div>
												<select id="sel_transportation_mode" name="sel_transportation_mode" area="area2" class="style5" style="width: 100%; height: 30px;"></select>
											</div>
										</div>
										<div style="height: 116px;">
											<div id="div_lbl24" class="lbl_norm2">Ulaşım Detayları: <font style="color: red;"></font></div>
											<div><textarea id="txt_transportation_detail" name="txt_transportation_detail" area="area2" class="style5" style="width: 100%; height: 88px;"></textarea></div>
										</div>
									</div>
								</div>
							</div>
							<div style="height: 10px;"></div>
						</div>
						<div id="div_accommodation" hidden>
							<div align="left" style="height: 17px; border-bottom: solid 2px blue;">
								<b>Konaklama Bilgileri</b>
							</div>
							<div style="height: 20px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div id="div_lbl25" class="lbl_norm2">Konaklama Başlangıç Tarihi: <font style="color: red;">*</font></div>
											<div><input type="date" id="inp_check-in_date" name="inp_check-in_date" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 58px;">
											<div id="div_lbl26" class="lbl_norm2">Konaklama Bitiş Tarihi:</div>
											<div><input type="date" id="inp_check-out_date" name="inp_check-out_date" area="area2" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 110px;">
											<div id="div_lbl27" class="lbl_norm2">Konaklama Detayları: <font style="color: red;"></font></div>
											<div><textarea id="txt_accommodation_detail" name="txt_accommodation_detail" area="area2" class="style5" style="width: 100%; height: 88px;"></textarea></div>
										</div>
									</div>
								</div>
							</div>
							<div style="height: 10px;"></div>
						</div>
						<div id="div_save" hidden>
							<div style="display: flex; height: 38px;">
								<div align="right" style="width: 100%;">
									<input type="button" id="inp_save" class="style5" style="width: 90px; height: 30px; font-weight: bold;" value="Kaydet ⮟" onClick="save_request();" />
								</div>
							</div>
						</div>
						<div style="height: 22px;"></div>
					</div>
				</div>
			</form>
		</center>
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
	</body>
</html>