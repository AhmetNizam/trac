<?php
	include("check_session.php");
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
		
		<!--<script src="/path/to/cdn/jquery.min.js"></script> -->
		<script src="./js/jquery.inputmask.min.js"></script>
		<!-- Allows data-input attribute -->
		<script src="./js/inputmask.binding.js"></script>

		<script type="text/javascript">

			$(document).ready(function() {
				$('#inp_birthdate').inputmask("gg.aa.yyyy");
				$('#inp_tckimlikno').inputmask("99999999999");
				$('#inp_phone').inputmask("9(999) 999 99 99");

				$('#inp_mail').keyup(function(e) {
					if($('#inp_mail').val().indexOf('@mlpcare.com') >= 0) {
						$.getJSON('./get_staff_information.php?mail=' + $('#inp_mail').val(),
							function(data) {
								if(data.Rows) {
									var rowData = data.Rows[0];
									set_staff_information(rowData);
								}
							}
						).fail(
							function(jqXHR, textStatus, errorThrown) {
								set_staff_information('');
							}
						);
					} else {
						set_staff_information('');
					}
				});
				
				$('#inp_trv_fcity').keyup(function(e) {
					set_city();
				});
				
				$('#inp_trv_tcity').keyup(function(e) {
					set_city();
				});
			});

			function generateUUID() {
				var d = new Date().getTime();//Timestamp
				var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now()*1000)) || 0; //Time in microseconds since page-load or 0 if unsupported
				return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
					var r = Math.random() * 16; //random number between 0 and 16
					if(d > 0){ //Use timestamp until depleted
						r = (d + r)%16 | 0;
						d = Math.floor(d/16);
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

			function get_list(list_table, field_name = '') {
				return new Promise((resolve, reject) => {
					$.getJSON('./get_selection_list.php?table_name=' + list_table + '&field_name=' + field_name,
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

			function fill_selection_list(sel_item, list_table, field_name = '') {
				get_list(list_table, field_name).then(data => {
					if(data.Rows) {
						var content = '<option value="">-- Seçiniz --</option>';
						if(sel_item.attr('id') == 'sel_trv_floc') {
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

			function reset_value(items) {
				$.each(items, function() { $(this).val(''); });
			}

			function writability(writable_items, readonly_items) {
				$.each(writable_items, function() { $(this).attr('readonly', false); });
				$.each(readonly_items, function() { $(this).attr('readonly', true); });
			}

			function set_staff_information(data) {
				$('#inp_name').val(data.name ? data.name : '');
				if($('#sel_req_own').val() == 1) {
					$('#inp_mail').val(data.mail ? data.mail : '');
				}
				$('#inp_location').val(data.location ? data.location : '');
				$('#inp_position').val(data.position ? data.position : '');
				$('#inp_department').val(data.department ? data.department : '');
			}

			function set_requesting_type(item) {
				reset_value([$('#inp_name'), $('#inp_birthdate'), $('#inp_identityno'), $('#inp_passportno'), $('#inp_phone'), $('#inp_mail'), $('#inp_location'), $('#inp_position'), $('#inp_department'), $('#sel_guests_country')]);

				if(item.val() == '') {
					toggle_visibility([], [$('#div_roi_personal_info')]);
				} else if(item.val() == '1') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_blank_line2'), $('#div_roi_staff_inp')], [$('#div_roi_staff_sel')]);
					writability([], [$('#inp_name'), $('#inp_mail')]);

					$.getJSON('./get_user_information.php',
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								set_staff_information(rowData);
							}
						}
					);

					$('#inp_birthdate').focus();
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_blank_line2'), $('#div_roi_staff_inp')], [$('#div_roi_staff_sel')]);
					writability([$('#inp_mail')], [$('#inp_name')]);
					$('#inp_mail').focus();
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_blank_line2'), $('#div_roi_staff_sel')], [$('#div_roi_staff_inp')]);
					writability([$('#inp_name'), $('#inp_mail')], []);
					$('#inp_name').focus();
				} else if(item.val() == '4') {
					toggle_visibility([$('#div_roi_personal_info')], [$('#div_blank_line2'), $('#div_roi_staff_inp'), $('#div_roi_staff_sel')]);
					writability([$('#inp_name'), $('#inp_mail')], []);
					$('#inp_name').focus();
				}
			}

			function set_travel_route(item) {
				if(item.attr('id') == 'rb1') {
					toggle_visibility([$('#div_lbl_floc1'), $('#div_lbl_tloc1')], [$('#div_roi_passport'), $('#div_lbl_floc2'), $('#div_lbl_tloc2')]);
				} else if(item.attr('id') == 'rb2') {
					toggle_visibility([$('#div_roi_passport'), $('#div_lbl_floc2'), $('#div_lbl_tloc2')], [$('#div_lbl_floc1'), $('#div_lbl_tloc1')]);
				}
				
			}

			function add_traveler() {
				if(!$('#rb1').prop('checked') && !$('#rb2').prop('checked')) {
					alert('\'Seyahat Rotası\' seçilmedi!');
					$('#div_lbl1').attr('class', 'lbl_alrt');
					return false;
				}

				var uuid = generateUUID();
				var content = '';
				content =	'	<div id="' + uuid + '-1" class="trv_tbl_ln1">' +
							'		<div class="trv_list1"><div class="trv_list4" style="width: 150px;">' + $('#inp_name').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list3" style="width: 100px;">' + $('#inp_birthdate').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list3" style="width: 100px;">' + $('#inp_identityno').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list3" style="width: 100px;">' + $('#inp_passportno').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list3" style="width: 120px;">' + $('#inp_phone').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list4" style="width: 250px;">' + $('#inp_mail').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list4" style="width: 250px;">' + $('#inp_position').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list4" style="width: 250px;">' + $('#inp_department').val() + '</div></div>' +
							'		<div class="trv_list1"><div class="trv_list4" style="width: 250px;">' + $('#inp_location').val() + '</div></div>' +
							'	</div>';

				$('#div_traveler_line_frame1').append(content);

				content =	'	<div id="' + uuid + '-2" class="trv_tbl_ln2">' +
							'		<div class="trv_tbl_cell">' +
							'			<div style="margin-top: 1px;">' +
							'				<img src="./images/edit.png" style="height: 90%; cursor: pointer;" title="Düzenle" onClick="edit_traveler(\'\');" />' +
							'			</div>' +
							'		</div>' +
							'		<div class="trv_tbl_cell">' +
							'			<div style="margin-top: 1px;">' +
							'				<img src="./images/delete.png" style="height: 90%; cursor: pointer;" title="Sil" onClick="del_traveler(\'' + uuid + '\');" />' +
							'			</div>' +
							'		</div>' +
							'	</div>';

				$('#div_traveler_line_frame2').append(content);

				toggle_visibility([$('#div_traveler_list')], []);
				return true;
			}

			function del_traveler(uuid) {
				alert(uuid);
				$('#' + uuid + '-1').remove();
				$('#' + uuid + '-2').remove();
			}

			function open_travel_info() {
				if($('#rb1').prop('checked')) {
					fill_location_list('220', $('#sel_trv_floc')).then(data => {
						if($('#sel_trv_floc').children().length > 0) {
							$('#sel_trv_tloc').html($('#sel_trv_floc').html());
							toggle_visibility([$('#div_travel_info'), $('#div_from_location'), $('#div_to_location')], []);
						} else {
							$('#sel_trv_tctr').html($('#sel_trv_fctr').html());
							toggle_visibility([$('#div_travel_info'), $('#div_from_country'), $('#div_to_country')], []);
						}
					});
				} else if($('#rb2').prop('checked')) {
					$('#sel_trv_tctr').html($('#sel_trv_fctr').html());
					toggle_visibility([$('#div_travel_info'), $('#div_from_country'), $('#div_to_country')], []);
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
					)
					.fail(function(error) { reject(error); })
				});
			}

			function set_country(ctrl_item, div_item1, location_item, div_item2, input_item, select_item) {
				reset_value([location_item, input_item, select_item]);
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
				if(ctrl_item.val() == '') {
					div_item.hide();
				} else if(ctrl_item.val() == '0') {
					if($('#rb1').prop('checked') == true || country_item.val() == '220') {
						fill_selection_list(select_item, 'city');
						toggle_visibility([div_item, select_item.parent()], [input_item.parent()]);
					} else {
						input_item.val('');
						toggle_visibility([div_item, input_item.parent()], [select_item.parent()]);
						input_item.prop('readonly', false);
					}
				} else {
					$.getJSON('./get_city_by_location.php?location_id=' + ctrl_item.val(),
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								input_item.val(rowData.name);
							}
						}
					).done(
						function() {
							toggle_visibility([div_item, input_item.parent()], [select_item.parent()]);
							input_item.prop('readonly', true);
							set_city();
						}
					);
				}
			}

			function set_city() {
				if( ($('#inp_trv_fcity').val() != '' || $('#sel_trv_fcity').val() != null) &&
				    ($('#inp_trv_tcity').val() != '' || $('#sel_trv_tcity').val() != null) ) {
					$('#div_travel_reason').show();
				}
			}

		</script>
	</head>
	<body background="./images/body_gray.png" topmargin="20">
		<center>
			<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="">
				<div align="center" class="style4" style="width: 900px; border: groove; background: url('./images/body.png');">
					<div style="width: 90%; font-size: 12px;">
						<div style="height: 40px;"></div>
						<div class="style4" style="height: 20px; border-width: 2px; border-bottom-style: solid; border-bottom-color: red; font-size: 15px;">
							<b>ULAŞIM ve KONAKLAMA BAŞVURU FORMU</b>
						</div>
						<div style="height: 10px;"></div>
						<div align="right" style="height: 15px;">
							<div><b>Talep Tarihi: </b><?php echo date("d.m.Y"); ?></div>
						</div>
						<div style="height: 10px;"></div>
						<div align="left" style="height: 13px; border-width: 2px; border-style: solid; border-color: #CDCDCD; background-color: #CDCDCD;">
							<b>&nbsp;Talep Sahibi Bilgileri</b>
						</div>
						<div style="border-width: 2px; border-style: solid; border-color: #CDCDCD;">
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div style="width: 3%;"></div>
								<div style="width: 94%;">
									<div style="display: flex;">
										<div align="left" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div id="div_roi_type">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div style="margin-top: 7px;">
																<b>Talep Sahibi: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<select id="sel_req_own" class="style5" style="width: 100%; height: 30px;" onChange="set_requesting_type($(this));">
																<option value="">-- Seçiniz --</option>
																<option value="1">Kendim</option>
																<option value="2">Personel (MLPCare)</option>
																<option value="3">Personel (Diğer)</option>
																<option value="4">Misafir</option>
															</select>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div align="right" style="width: 50%;">
											<div align="left" style="width: 97%;">
												<div id="div_roi_type1">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div id="div_lbl1" class="lbl_norm" style="margin-top: 7px;">Seyahat Rotası: <font style="color: red;">*</font>
															</div>
														</div>
														<div style="width: 65%;">
															<div style="width: 177px; display: flex; margin-top: 5px;">
																<div style="margin-top: 2px;">Yurt İçi</div>
																<div style="width: 24px;">
																	<input type="radio" id="rb1" name="rb_tinf" class="style5" onChange="set_travel_route($(this));" />
																</div>
																<div style="width: 20px;"></div>
																<div style="margin-top: 2px;">Yurt Dışı</div>
																<div style="width: 24px;">
																	<input type="radio" id="rb2" name="rb_tinf" class="style5" onChange="set_travel_route($(this));" />
																</div>
																<div style="width: 15px;"></div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div id="div_roi_personal_info" hidden>
										<div id="div_blank_line1">
											<div style="height: 2px; border-width: 2px; border-bottom-style: solid; border-bottom-color: #CDCDCD;"></div>
											<div style="height: 10px;"></div>
										</div>
										<div style="display: flex;">
											<div align="left" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div style="margin-top: 7px;">
																<b>Adı Soyadı: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<input id="inp_name" type="text" class="style5" style="width: 100%; height: 30px;" readonly />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div style="margin-top: 7px;">
																<b>Doğum Tarihi: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<input id="inp_birthdate" type="date" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div style="margin-top: 7px;">
																<b>Kimlik No: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<input id="inp_identityno" type="text" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
												</div>
											</div>
											<div align="right" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div style="display: flex; height: 38px;">
														<div style="width: 35%;">
															<div style="margin-top: 7px;">
																<b>Telefon No: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<input id="inp_phone" type="text" class="style5" style="width: 100%; height: 30px;" />
														</div>
													</div>
													<div style="display: flex; height: 38px;">
														<div style="width: 35%">
															<div style="margin-top: 7px;">
																<b>Mail Adresi: <font style="color: red;">*</font></b>
															</div>
														</div>
														<div style="width: 65%;">
															<input id="inp_mail" type="text" class="style5" style="width: 100%; height: 30px;" readonly />
														</div>
													</div>
													<div id="div_roi_passport" hidden>
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Pasaport No: <font style="color: red;">*</font></b>
																</div>
															</div>
															<div style="width: 65%;">
																<input id="inp_passportno" type="text" class="style5" style="width: 100%; height: 30px;" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="div_blank_line2" hidden>
											<div style="height: 2px; border-width: 2px; border-bottom-style: solid; border-bottom-color: #CDCDCD;"></div>
											<div style="height: 10px;"></div>
										</div>
										<div id="div_roi_staff_inp" hidden>
											<div style="display: flex;">
												<div align="left" style="width: 50%;">
													<div align="left" style="width: 97%;">
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Görevi / Ünvanı:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<input id="inp_position" type="text" class="style5" style="width: 100%; height: 30px;" readonly />
															</div>
														</div>
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Departmanı:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<input id="inp_department" type="text" class="style5" style="width: 100%; height: 30px;" readonly />
															</div>
														</div>
													</div>
												</div>
												<div align="right" style="width: 50%;">
													<div align="left" style="width: 97%;">
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Çalışma Şubesi:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<input id="inp_location" type="text" class="style5" style="width: 100%; height: 30px;" readonly />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="div_roi_staff_sel" hidden>
											<div style="display: flex;">
												<div align="left" style="width: 50%;">
													<div align="left" style="width: 97%;">
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Görev Ünvanı:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<select id="sel_position" class="style5" style="width: 100%; height: 30px;"></select>
															</div>
														</div>
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Departmanı:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<select id="sel_department" class="style5" style="width: 100%; height: 30px;"></select>
															</div>
														</div>
													</div>
												</div>
												<div align="right" style="width: 50%;">
													<div align="left" style="width: 97%;">
														<div style="display: flex; height: 38px;">
															<div style="width: 35%;">
																<div style="margin-top: 7px;">
																	<b>Çalışma Şubesi:</b>
																</div>
															</div>
															<div style="width: 65%;">
																<select id="sel_location" class="style5" style="width: 100%; height: 30px;"></select>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="div_blank_line3">
											<div style="height: 2px; border-width: 2px; border-bottom-style: solid; border-bottom-color: #CDCDCD;"></div>
											<div style="height: 10px;"></div>
										</div>
										<div style="display: flex;">
											<div align="left" style="width: 50%;">
												<div align="left" style="width: 97%;">
													<div id="div_roi_add">
														<div style="display: flex; height: 38px;">
															<div style="width: 100%;">
																<input id="inp_add" type="button" class="style5" style="width: 25%; height: 30px; font-weight: bold;" value="Ekle +" onClick="add_traveler();" />
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div id="div_traveler_list" hidden>
											<div align="left" style="height: 13px; border-width: 2px; border-style: solid; border-color: #B5D4EB; background-color: #B5D4EB;">
												<b>&nbsp;Talep Eden / Edenler</b>
											</div>
											<div class="trv_tbl">
												<div id="div_traveler_line_frame1" class="trv_tbl_frm1">
													<div class="trv_tbl_ln1" style="background: #D7D7D7;">
														<div class="trv_list1"><div class="trv_list2" style="width: 150px;">Adı Soyadı</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 100px;">Doğum Tarihi</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 100px;">Kimlik No</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 100px;">Pasaport No</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 120px;">Telefon No</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 250px;">Mail Adresi</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 250px;">Görevi / Ünvanı:</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 250px;">Departmanı:</div></div>
														<div class="trv_list1"><div class="trv_list2" style="width: 250px;">Çalışma Şubesi</div></div>
													</div>
												</div>
												<div id="div_traveler_line_frame2" class="trv_tbl_frm2">
													<div class="trv_tbl_ln2" style="background: #D7D7D7;">
														<div class="trv_tbl_cell" style="width: 100%;"></div>
													</div>
												</div>
											</div>
											<div style="height: 10px;"></div>
											<div style="display: flex;">
												<div align="left" style="width: 50%;">
													<div align="left" style="width: 97%;">
														<div id="div_roi_cnt">
															<div style="display: flex; height: 38px;">
																<div style="width: 100%;">
																	<input id="inp_cnt" type="button" class="style5" style="width: 25%; height: 30px; font-weight: bold;" value="Devam >>" onClick="open_travel_info();" />
																</div>
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
						<div align="left" style="height: 17px; border-width: 2px; border-bottom-style: solid; border-bottom-color: blue;">
							<b>Seyahat Bilgileri</b>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_travel_info" hidden>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div id="div_from_country" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b>Nereden: <font style="color: red;">*</font></b>
										</div>
										<div>
											<select id="sel_trv_fctr" class="style5" style="width: 100%; height: 30px;" onChange="set_country($(this), $('#div_from_location'), $('#sel_trv_floc'), $('#div_from_city'), $('#inp_trv_fcity'), $('#sel_trv_fcity'));"></select>
										</div>
									</div>
									<div id="div_from_location" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b><div id="div_lbl_floc1" hidden>Nereden:</div><div id="div_lbl_floc2" hidden>Lokasyon:</div> <font style="color: red;">*</font></b>
										</div>
										<div>
											<select id="sel_trv_floc" class="style5" style="width: 100%; height: 30px;" onChange="set_location($(this), $('#sel_trv_fctr'), $('#div_from_city'), $('#inp_trv_fcity'), $('#sel_trv_fcity'));"></select>
										</div>
									</div>
									<div id="div_from_city" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b>Şehir: <font style="color: red;">*</font></b>
										</div>
										<div>
											<div hidden><select id="sel_trv_fcity" class="style5" style="width: 100%; height: 30px;" onChange="set_city();"></select></div>
											<div hidden><input id="inp_trv_fcity" type="text" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div id="div_to_country" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b>Nereye: <font style="color: red;">*</font></b>
										</div>
										<div>
											<select id="sel_trv_tctr" class="style5" style="width: 100%; height: 30px;" onChange="set_country($(this), $('#div_to_location'), $('#sel_trv_tloc'), $('#div_to_city'), $('#inp_trv_tcity'), $('#sel_trv_tcity'));"></select>
										</div>
									</div>
									<div id="div_to_location" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b><div id="div_lbl_tloc1" hidden>Nereye:</div><div id="div_lbl_tloc2" hidden>Lokasyon:</div> <font style="color: red;">*</font></b>
										</div>
										<div>
											<select id="sel_trv_tloc" class="style5" style="width: 100%; height: 30px;" onChange="set_location($(this), $('#sel_trv_tctr'), $('#div_to_city'), $('#inp_trv_tcity'), $('#sel_trv_tcity'));"></select>
										</div>
									</div>
									<div id="div_to_city" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b>Şehir: <font style="color: red;">*</font></b>
										</div>
										<div>
											<div hidden><select id="sel_trv_tcity" class="style5" style="width: 100%; height: 30px;" onChange="set_city();"></select></div>
											<div hidden><input id="inp_trv_tcity" type="text" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
							</div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div id="div_travel_reason" align="left" style="width: 97%; height: 58px;" hidden>
										<div style="height: 17px;">
											<b>Seyahat Nedeni: <font style="color: red;">*</font></b>
										</div>
										<div>
											<select id="sel_rea_trv" class="style5" style="width: 100%; height: 30px;" onChange="toggle_visibility([$('#div_transport_accommodation')], []);"></select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div style="height: 20px;"></div>
						<div align="left" style="height: 17px; border-width: 2px; border-bottom-style: solid; border-bottom-color: blue;">
							<b>Ulaşım ve Konaklama Bilgileri</b>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_transport_accommodation" align="center" style="height: 25px;" hidden>
							<!-- <div style="width: 490px; height: 25px; text-align: left;">Lütfen talep türünü seçiniz.</div> -->
							<div align="left" style="display: flex; width: 490px; margin-left: -10px;">
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_transport'), $('#div_accommodation')], []);" />
								</div>
								<div style="margin-top: 2px;">Ulaşım ve Konaklama Talebi</div>
								<div style="width: 20px;"></div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_transport')], [$('#div_accommodation')]);" />
								</div>
								<div style="margin-top: 2px;">Ulaşım Talebi</div>
								<div style="width: 20px;"></div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_accommodation')], [$('#div_transport')]);" />
								</div>
								<div style="margin-top: 2px;">Konaklama Talebi</div>
								<div style="width: 20px;"></div>
								<div><font style="color: red; font-weight: bold;">*</font></div>
							</div>
						</div>
						<div id="div_transport" hidden>
							<div align="left" style="height: 17px; border-width: 2px; border-bottom-style: solid; border-bottom-color: blue;">
								<b>Ulaşım Bilgileri</b>
							</div>
							<div style="height: 20px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div style="height: 17px;"><b>Gidiş Tarihi: <font style="color: red;">*</font></b></div>
											<div><input type="date" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 58px;">
											<div style="height: 17px;"><b>Dönüş Tarihi:</b></div>
											<div><input type="date" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 52px;">
											<div style="height: 22px;"><b>Transfer İhtiyaç Durumu: <font style="color: red;">*</font></b></div>
											<div style="display: flex; margin-left: -5px;">
												<div style="width: 24px;">
													<input type="radio" name="rb_tns" class="style5" onClick="toggle_visibility([$('#div_nftd')], []);" />
												</div>
												<div style="margin-top: 2px;">Var</div>
												<div style="width: 20px;"></div>
												<div style="width: 24px;">
													<input type="radio" name="rb_tns" class="style5" onClick="toggle_visibility([], [$('#div_nftd')]);" />
												</div>
												<div style="margin-top: 2px;">Yok</div>
											</div>
										</div>
										<div id="div_nftd" style="height: 58px;" hidden>
											<div style="height: 17px;"><b>Transfer İhtiyaç Detayı: <font style="color: red;">*</font></b></div>
											<div><input type="text" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div style="height: 17px;"><b>Ulaşım Yöntemi: <font style="color: red;">*</font></b></div>
											<div>
												<select id="sel_tra_met" class="style5" style="width: 100%; height: 30px;"></select>
											</div>
										</div>
										<div style="height: 116px;">
											<div style="height: 17px;"><b>Ulaşım Detayları: <font style="color: red;">*</font></b></div>
											<div><textarea class="style5" style="width: 100%; height: 88px;"></textarea></div>
										</div>
									</div>
								</div>
							</div>
							<div style="height: 10px;"></div>
						</div>
						<div id="div_accommodation" hidden>
							<div align="left" style="height: 17px; border-width: 2px; border-bottom-style: solid; border-bottom-color: blue;">
								<b>Konaklama Bilgileri</b>
							</div>
							<div style="height: 20px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 58px;">
											<div style="height: 17px;"><b>Konaklama Başlangıç Tarihi: <font style="color: red;">*</font></b></div>
											<div><input type="date" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
										<div style="height: 58px;">
											<div style="height: 17px;"><b>Konaklama Bitiş Tarihi:</b></div>
											<div><input type="date" class="style5" style="width: 100%; height: 30px;" /></div>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div style="height: 110px;">
											<div style="height: 17px;"><b>Konaklama Detayları: <font style="color: red;">*</font></b></div>
											<div><textarea class="style5" style="width: 100%; height: 88px;"></textarea></div>
										</div>
									</div>
								</div>
							</div>
							<div style="height: 10px;"></div>
						</div>
						<div style="height: 22px;"></div>
					</div>
				</div>
			</form>
		</center>
		<script type="text/javascript">
			// fill_selection_list($('#sel_req_own'), 'user', 'CONCAT(name, " ", surname)');
			fill_selection_list($('#sel_location'), 'location');
			fill_selection_list($('#sel_position'), 'position');
			fill_selection_list($('#sel_department'), 'department');
			// fill_selection_list($('#sel_guests_country'), 'country');
			fill_selection_list($('#sel_trv_fctr'), 'country');
			// fill_selection_list($('#sel_trv_floc'), 'location');
			// fill_selection_list($('#sel_trv_fcity'), 'city');
			fill_selection_list($('#sel_rea_trv'), 'reason_for_travel');
			fill_selection_list($('#sel_tra_met'), 'transportation_method');
		</script>
	</body>
</html>