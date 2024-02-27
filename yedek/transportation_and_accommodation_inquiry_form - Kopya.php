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
				$('#inp_roi_birthdate').inputmask("gg.aa.yyyy");
				$('#inp_roi_tckimlikno').inputmask("99999999999");
				$('#inp_roi_phone').inputmask("9(999) 999 99 99");
			});

			function toggle_visibility(visible_items, unvisible_items) {
				$.each(unvisible_items, function() { $(this).hide(); });
				$.each(visible_items, function() { $(this).show(); });
			}

			function choice_travel_country(ctrl_item, visible_items, reset_items) {
				$.each(reset_items, function() { $(this).val(''); });

				if(ctrl_item.val().length > 0) {
					$.each(visible_items, function() { $(this).show(); });
				} else {
					$.each(visible_items, function() { $(this).hide(); });
				}
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
						if(sel_item.attr('id') == 'sel_req_own') {
							content += '<option value="1">Kendim =></option>';
							content += '<option value="2">Personel =></option>';
							content += '<option value="3">Misafir =></option>';
						} else if(sel_item.attr('id') == 'sel_trv_loc') {
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

			function set_user_information(item) {
				$('#div_working_location').html('');
				$('#div_job_title').html('');
				$('#div_department').html('');

				if(item.val() == '') {
					toggle_visibility([], [$('#div_roi_personal_info'), $('#div_roi_name'), $('#div_roi_staff'), $('#div_roi_staff_input'), $('#div_roi_guest')]);
				} else if(item.val() == '1') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_roi_name'), $('#div_roi_staff_input')], [$('#div_roi_staff'), $('#div_roi_guest')]);
				} else if(item.val() == '2') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_roi_name'), $('#div_roi_staff_input')], [$('#div_roi_staff'), $('#div_roi_guest')]);
				} else if(item.val() == '3') {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_roi_name'), $('#div_roi_guest')], [$('#div_roi_staff'), $('#div_roi_staff_input')]);
				} else {
					toggle_visibility([$('#div_roi_personal_info'), $('#div_roi_staff_input')], [$('#div_roi_name'), $('#div_roi_staff'), $('#div_roi_guest')]);
					$.getJSON('./get_user_information.php?user_id=' + item.val(),
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								$('#div_working_location').html(rowData.loc_name);
								$('#div_job_title').html(rowData.pos_name);
								$('#div_department').html(rowData.dep_name);
							}
						}
					);
				}
			}

			function set_travel_city(item) {
				if(item.val() == '') {
					toggle_visibility([], [$('#div_tl_tc'), $('#div_tl_tc_sel'), $('#div_tl_tc_lbl')]);
				} else if(item.val() == '0') {
					toggle_visibility([$('#div_tl_tc'), $('#div_tl_tc_sel')], [$('#div_tl_tc_lbl')]);
				} else {
					$.getJSON('./get_city_by_location.php?location_id=' + item.val(),
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								$('#div_tl_tc_lbl').html(rowData.name);
							}
						}
					);
					toggle_visibility([$('#div_tl_tc'), $('#div_tl_tc_lbl')], [$('#div_tl_tc_sel')]);
				}
			}

		</script>
	</head>
	<body background="./images/body_gray.png" topmargin="20">
		<center>
			<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="">
				<div align="center" class="style4" style="width: 900px; border: groove">
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
								<div style="width: 2%;"></div>
								<div style="display: flex; width: 96%;">
									<div align="left" style="width: 50%;">
										<div align="left">
											<div id="div_roi_type">
												<div style="display: flex; height: 38px;">
													<div style="width: 35%;">
														<div style="margin-top: 7px;">
															<b>Talep Sahibi: <font style="color: red;">*</font></b>
														</div>
													</div>
													<div style="width: 65%;">
														<select id="sel_req_own" class="style5" style="width: 95%; height: 30px;" onChange="set_user_information($(this));">
															<option value="">-- Seçiniz --</option>
															<option value="1">Kendim =></option>
															<option value="2">Personel =></option>
															<option value="3">Misafir =></option>
														</select>
													</div>
												</div>
											</div>
											<div id="div_roi_personal_info" hidden>
												<div id="div_roi_name" style="display: flex; height: 38px;" hidden>
													<div style="width: 35%;">
														<div style="margin-top: 7px;">
															<b>Adı Soyadı: <font style="color: red;">*</font></b>
														</div>
													</div>
													<div style="width: 65%;">
														<input type="text" class="style5" style="width: 95%; height: 30px;" />
													</div>
												</div>
												<div style="display: flex; height: 38px;">
													<div style="width: 35%;">
														<div style="margin-top: 7px;">
															<b>Doğum Tarihi: <font style="color: red;">*</font></b>
														</div>
													</div>
													<div style="width: 65%;">
														<input id="inp_roi_birtdate" type="date" class="style5" style="width: 95%; height: 30px;" />
													</div>
												</div>
												<div style="display: flex; height: 38px;">
													<div style="width: 35%;">
														<div style="margin-top: 7px;">
															<b>Kimlik No: <font style="color: red;">*</font></b>
														</div>
													</div>
													<div style="width: 65%;">
														<input id="inp_roi_tckimlikno" type="text" class="style5" style="width: 95%; height: 30px;" />
													</div>
												</div>
												<div style="display: flex; height: 38px;">
													<div style="width: 35%;">
														<div style="margin-top: 7px;">
															<b>Telefon Numarası: <font style="color: red;">*</font></b>
														</div>
													</div>
													<div style="width: 65%;">
														<input id="inp_roi_phone" type="text" class="style5" style="width: 95%; height: 30px;" />
													</div>
												</div>
											</div>
										</div>
									</div>
									<div align="right" style="width: 50%;">
										<div id="div_roi_staff" align="left" style="width: 97%;" hidden>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Çalışma Şubesi:</b>
													</div>
												</div>
												<div id="div_working_location" style="width: 72%; margin-top: 7px;"></div>
											</div>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Görev Ünvanı:</b>
													</div>
												</div>
												<div id="div_job_title" style="width: 72%; margin-top: 7px;"></div>
											</div>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Departmanı:</b>
													</div>
												</div>
												<div id="div_department" style="width: 72%; margin-top: 7px;"></div>
											</div>
										</div>
										<div id="div_roi_staff_input" align="left" style="width: 97%;" hidden>
											<div id="div_roi_mail" style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Mail Adresi: <font style="color: red;">*</font></b>
													</div>
												</div>
												<div id="div_mail_address_staff" align="right" style="width: 67%;">
													<input type="text" class="style5" style="width: 95%; height: 30px;" />
												</div>
											</div>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Çalışma Şubesi:</b>
													</div>
												</div>
												<div id="div_working_location_staff" align="right" style="width: 67%;">
													<select id="sel_location" class="style5" style="width: 95%; height: 30px;" readonly></select>
												</div>
											</div>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Görev Ünvanı:</b>
													</div>
												</div>
												<div id="div_job_title_staff" align="right" style="width: 67%;">
													<select id="sel_position" class="style5" style="width: 95%; height: 30px;" readonly></select>
												</div>
											</div>
											<div style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Departmanı:</b>
													</div>
												</div>
												<div id="div_department_staff" align="right" style="width: 67%;">
													<select id="sel_department" class="style5" style="width: 95%; height: 30px;" readonly></select>
												</div>
											</div>
										</div>
										<div id="div_roi_guest" align="left" style="width: 97%;" hidden>
											<div id="div_roi_mail" style="display: flex; height: 38px;">
												<div style="width: 28%;">
													<div style="margin-top: 7px;">
														<b>Bulunduğu Ülke: <font style="color: red;">*</font></b>
													</div>
												</div>
												<div id="div_guests_country" align="right" style="width: 67%;">
													<select id="sel_guests_country" class="style5" style="width: 95%; height: 30px;"></select>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div style="width: 2%;"></div>
							</div>
							<div style="height: 10px;"></div>
						</div>
						<div style="height: 30px;"></div>
						<div align="left" style="height: 17px; border-width: 2px; border-bottom-style: solid; border-bottom-color: blue;">
							<b>Seyahat Bilgileri</b>
						</div>
						<div style="height: 20px;"></div>
						<div align="center" style="height: 20px;">
							<div style="width: 177px; display: flex;">
								<div style="margin-top: 2px;">Yurt İçi</div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tinf" class="style5" onClick="toggle_visibility([$('#div_ti1'), $('#div_ti3')], [$('#div_ti2')]);" />
								</div>
								<div style="width: 20px;"></div>
								<div style="margin-top: 2px;">Yurt Dışı</div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tinf" class="style5" onClick="toggle_visibility([$('#div_ti2'), $('#div_ti3')], [$('#div_ti1')]);" />
								</div>
								<div style="width: 15px;"></div>
								<div><font style="color: red; font-weight: bold;">*</font></div>
							</div>
						</div>
						<div style="height: 10px;"></div>
						<div id="div_ti1" hidden>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div style="width: 97%; height: 58px;">
										<div style="height: 17px;"><b>Seyahat Lokasyonu: <font style="color: red;">*</font></b></div>
										<div>
											<select id="sel_trv_loc" class="style5" style="width: 100%; height: 30px;" onChange="set_travel_city($(this));"></select>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div id="div_tl_tc" hidden>
											<div style="height: 58px;">
												<div style="height: 17px;"><b>Seyahat Edilecek Şehir: <font style="color: red;">*</font></b></div>
												<div id="div_tl_tc_sel" hidden><select id="sel_trv_city" class="style5" style="width: 100%; height: 30px;"></select></div>
												<div id="div_tl_tc_lbl" style="width: 72%; margin-top: 7px;" hidden></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="div_ti2" hidden>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div style="width: 97%; height: 58px;">
										<div style="height: 17px;"><b>Pasaport Numarası: <font style="color: red;">*</font></b></div>
										<div><input type="text" class="style5" style="width: 100%; height: 30px;" /></div>
									</div>
								</div>
							</div>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div style="width: 97%; height: 58px;">
										<div style="height: 17px;"><b>Seyahat Ülkesi: <font style="color: red;">*</font></b></div>
										<div>
											<select id="sel_trv_ctr" class="style5" style="width: 100%; height: 30px;" onChange="choice_travel_country($(this), [$('#div_ti_tc')], [$('#inp_ti_tc')]);"></select>
										</div>
									</div>
								</div>
								<div align="right" style="width: 50%;">
									<div align="left" style="width: 97%;">
										<div id="div_ti_tc" hidden>
											<div style="height: 58px;">
												<div style="height: 17px;"><b>Yurtdışı Şehir Adı: <font style="color: red;">*</font></b></div>
												<div><input id="inp_ti_tc" type="text" class="style5" style="width: 100%; height: 30px;" /></div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div id="div_ti3" hidden>
							<div style="display: flex;">
								<div align="left" style="width: 50%;">
									<div style="width: 97%; height: 58px;">
										<div style="height: 17px;"><b>Seyahat Nedeni: <font style="color: red;">*</font></b></div>
										<div>
											<select id="sel_rea_trv" class="style5" style="width: 100%; height: 30px;"></select>
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
						<div align="center" style="height: 40px;">
							<div style="width: 490px; height: 25px; text-align: left;">Lütfen talep türünü seçiniz.</div>
							<div align="left" style="display: flex; width: 490px; margin-left: -10px;">
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_tai1'), $('#div_tai2')], []);" />
								</div>
								<div style="margin-top: 2px;">Ulaşım ve Konaklama Talebi</div>
								<div style="width: 20px;"></div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_tai1')], [$('#div_tai2')]);" />
								</div>
								<div style="margin-top: 2px;">Ulaşım Talebi</div>
								<div style="width: 20px;"></div>
								<div style="width: 24px;">
									<input type="radio" name="rb_tainf" class="style5" onClick="toggle_visibility([$('#div_tai2')], [$('#div_tai1')]);" />
								</div>
								<div style="margin-top: 2px;">Konaklama Talebi</div>
								<div style="width: 20px;"></div>
								<div><font style="color: red; font-weight: bold;">*</font></div>
							</div>
						</div>
						<div style="height: 20px;"></div>
						<div id="div_tai1" hidden>
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
							<div style="height: 20px;"></div>
						</div>
						<div id="div_tai2" hidden>
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
							<div style="height: 20px;"></div>
						</div>
					</div>
				</div>
			</form>
		</center>
		<script type="text/javascript">
			// fill_selection_list($('#sel_req_own'), 'user', 'CONCAT(name, " ", surname)');
			fill_selection_list($('#sel_location'), 'location');
			fill_selection_list($('#sel_position'), 'position');
			fill_selection_list($('#sel_department'), 'department');
			fill_selection_list($('#sel_guests_country'), 'country');
			fill_selection_list($('#sel_trv_loc'), 'location');
			fill_selection_list($('#sel_trv_city'), 'city');
			fill_selection_list($('#sel_trv_ctr'), 'country');
			fill_selection_list($('#sel_rea_trv'), 'reason_for_travel');
			fill_selection_list($('#sel_tra_met'), 'transportation_method');
		</script>
	</body>
</html>