<?php
	require("./library.php");

	$request = $_SESSION['request'];
	$traveler_list = $request['traveler_list'];
	$travel_info = $request['travel_info'];
	$transportation_info = $request['transportation_info'];
	$accommodation_info = $request['accommodation_info'];
?>

					<div id="div_preview" align="center">
						<div style="width: 90%; font-size: 12px;">
							<div style="height: 40px;"></div>
							<div class="heading">ULAŞIM ve KONAKLAMA TALEBİ</div>
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 33%;">
									<div class="databox">
										<div class="lbl_norm2">Seyahat Rotası:</div>
										<div><?php echo $travel_info['travel_route']; ?></div>
									</div>
								</div>
								<div align="left" style="width: 33%;">
									<div class="databox">
										<div class="lbl_norm2">Talep Tarihi:</div>
										<div><?php echo $request['request_date']; ?></div>
									</div>
								</div>
							</div>
							<div style="height: 20px;"></div>
							<div class="subheading">Seyahat Edecekler</div>
							<div style="height: 10px;"></div>
							<div align="left" style="overflow: auto;">
								<table class="a_trv_tbl">
									<thead>
										<tr class="trv_thead">
											<th class="a_trv_cell_th"></th>
											<th class="a_trv_cell_th">Adı</th>
											<th class="a_trv_cell_th">Soyadı</th>
											<th class="a_trv_cell_th">Doğum Tarihi</th>
											<th class="a_trv_cell_th">Telefon No</th>
											<th class="a_trv_cell_th">Mail Adresi</th>
										</tr>
									</thead>
									<tbody>
<?php foreach($traveler_list as $traveler) { ?>
										<tr class="trv_tbody">
											<td class="a_trv_cell_tb"><?php echo $traveler['typename']; ?></td>
											<td class="a_trv_cell_tb"><?php echo $traveler['name']; ?></td>
											<td class="a_trv_cell_tb"><?php echo $traveler['surname']; ?></td>
											<td class="a_trv_cell_tb"><?php echo date('d.m.Y', strtotime($traveler['birthdate'])); ?></td>
											<td class="a_trv_cell_tb"><?php echo $traveler['phone']; ?></td>
											<td class="a_trv_cell_tb"><?php echo $traveler['mail']; ?></td>
										</tr>
<?php } ?>
									</tbody>
								</table>
							</div>
							<div style="height: 20px;"></div>
							<div class="subheading">Seyahat Bilgileri</div>
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 33%;">
<?php							if($travel_info['travel_routeid'] == '2') { ?>
									<div class="databox">
										<div class="lbl_norm2">Nereden:</div>
										<div><?php echo $travel_info['from_country']; ?></div>
									</div>
<?php							} ?>
									<div class="databox">
										<div class="lbl_norm2"><?php if($travel_info['travel_routeid'] == '2') { echo 'Şehir:'; } else { echo 'Nereden:'; } ?></div>
										<div><?php echo $travel_info['from_city']; ?></div>
									</div>
<?php							if($travel_info['from_locationid']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Lokasyon:</div>
										<div><?php echo $travel_info['from_location']; ?></div>
									</div>
<?php							} ?>
								</div>
								<div align="left" style="width: 33%;">
<?php							if($travel_info['travel_routeid'] == '2') { ?>
									<div class="databox">
										<div class="lbl_norm2">Nereye:</div>
										<div><?php echo $travel_info['to_country']; ?></div>
									</div>
<?php							} ?>
									<div class="databox">
										<div class="lbl_norm2"><?php if($travel_info['travel_routeid'] == '2') { echo 'Şehir:'; } else { echo 'Nereye:'; } ?></div>
										<div><?php echo $travel_info['to_city']; ?></div>
									</div>
<?php							if($travel_info['to_locationid']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Lokasyon:</div>
										<div><?php echo $travel_info['to_location']; ?></div>
									</div>
<?php							} ?>
								</div>
								<div align="left" style="width: 34%;">
									<div class="databox">
										<div class="lbl_norm2">Seyahat Nedeni:</div>
										<div><?php echo $travel_info['travel_reason']; ?></div>
									</div>
								</div>
							</div>
							<div style="height: 20px;"></div>
<?php					if($request['transportation_on_off']) { ?>
							<div class="subheading">Ulaşım Bilgileri</div>
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 33%;">
									<div class="databox">
										<div class="lbl_norm2">Gidiş Tarihi:</div>
										<div><?php echo $transportation_info['departure_date'] ? date('d.m.Y', strtotime($transportation_info['departure_date'])) : ''; ?></div>
									</div>
<?php							if($transportation_info['return_date']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Dönüş Tarihi:</div>
										<div><?php echo $transportation_info['return_date'] ? date('d.m.Y', strtotime($transportation_info['return_date'])) : ''; ?></div>
									</div>
<?php							} ?>
								</div>
								<div align="left" style="width: 33%;">
									<div class="databox">
										<div class="lbl_norm2">Ulaşım Yöntemi:</div>
										<div><?php echo $transportation_info['transportation_mode']; ?></div>
									</div>
<?php							if($transportation_info['transportation_detail']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Ulaşım Detayları:</div>
										<div><?php echo $transportation_info['transportation_detail']; ?></div>
									</div>
<?php							} ?>
								</div>
								<div align="left" style="width: 34%;">
									<div class="databox">
										<div class="lbl_norm2">Transfer İhtiyaç Durumu:</div>
										<div><?php echo $transportation_info['transfer_need_situation_name']; ?></div>
									</div>
<?php							if($transportation_info['transfer_need_situation'] == '1') { ?>
									<div class="databox">
										<div class="lbl_norm2">Transfer İhtiyaç Detayı:</div>
										<div><?php echo $transportation_info['transfer_need_detail']; ?></div>
									</div>
<?php							} ?>
								</div>
							</div>
							<div style="height: 20px;"></div>
<?php					} ?>
<?php					if($request['accommodation_on_off']) { ?>
							<div class="subheading">Konaklama Bilgileri</div>
							<div style="height: 10px;"></div>
							<div style="display: flex;">
								<div align="left" style="width: 33%;">
									<div class="databox">
										<div class="lbl_norm2">Konaklama Başlangıç Tarihi:</div>
										<div><?php echo $accommodation_info['check-in_date'] ? date('d.m.Y', strtotime($accommodation_info['check-in_date'])) : ''; ?></div>
									</div>
<?php							if($accommodation_info['check-out_date']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Konaklama Bitiş Tarihi:</div>
										<div><?php echo $accommodation_info['check-out_date'] ? date('d.m.Y', strtotime($accommodation_info['check-out_date'])) : ''; ?></div>
									</div>
<?php							} ?>
								</div>
								<div align="left" style="width: 33%;">
<?php							if($accommodation_info['accommodation_detail']) { ?>
									<div class="databox">
										<div class="lbl_norm2">Konaklama Detayları:</div>
										<div><?php echo $accommodation_info['accommodation_detail']; ?></div>
									</div>
<?php							} ?>
								</div>
							</div>
							<div style="height: 20px;"></div>
<?php					} ?>
							<div class="subheading" style="height: 2px;"></div>
							<div style="height: 10px;"></div>
							<div id="div_save_buttons" align="right">
								<div style="display: flex; width: 300px;">
									<div>
										<input type="button" id="btn_next" class="btn" value="İleri ⮞" onClick="toggle_visibility([$('#div_approval_buttons')], [$('#div_save_buttons')]);" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_cancel" class="btn" value="İptal ✖" onClick="cancel_request();" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_edit" class="btn" value="Düzenle ✎" onClick="toggle_visibility([$('#div_form_page')], [$('#div_approve_page')]);" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_approve" class="btn" value="Onayla ✔" onClick="save_request();" />
									</div>
								</div>
							</div>
							<div id="div_approval_buttons" align="center" hidden>
								<div style="display: flex; width: 300px;">
									<div>
										<input type="button" id="btn_back" class="btn" value="Geri ⮜" onClick="toggle_visibility([$('#div_save_buttons')], [$('#div_approval_buttons')]);" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_reject" class="btn_red" value="Reddet ✖" onClick="window.open('https://www.google.com.tr', '_blank');" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_revise" class="btn_yellow" value="Revize ✎" onClick="window.open('https://www.google.com.tr', '_blank');" />
									</div>
									<div style="width: 15px;"></div>
									<div>
										<input type="button" id="btn_approve" class="btn_green" value="Onayla ✔" onClick="window.open('https://www.google.com.tr', '_blank');" />
									</div>
								</div>
							</div>
							<div style="height: 22px;"></div>
						</div>
					</div>
					<div id="div_completion" align="center" hidden>
						<div style="height: 100px;"></div>
						<div><img src="images/ok.png" /></div>
						<div style="height: 20px;"></div>
						<div>Ulaşım ve Konaklama Talebi oluşturuldu.</div>
						<div style="height: 20px;"></div>
						<div align="center">
							<div style="display: flex; width: 195px;">
								<div>
									<input type="button" id="btn_back2" class="btn" value="Geri ⮜" onClick="toggle_visibility([$('#div_preview')], [$('#div_completion')]);" />
								</div>
								<div style="width: 15px;"></div>
								<div>
									<input type="button" id="btn_ok" class="btn" value="Tamam ✔" onClick="window.open('./entry_form.php', '_self');" />
								</div>
							</div>
						</div>
						<div style="height: 100px;"></div>
					</div>
				