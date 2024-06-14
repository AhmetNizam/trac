<?php
	require("./library.php");

	$reservation = $_SESSION['reservation'];
	$transportation_on_off = $reservation['transportation_on_off'];
	$transportation_info = $reservation['transportation_info'];
	$departure = $transportation_info['departure'];
	$return = $transportation_info['return'];
	$accommodation_on_off = $reservation['accommodation_on_off'];
	$accommodation_info = $reservation['accommodation_info'];
?>

					<div id="div_preview_page">
						<div style="height: 40px;"></div>
						<div class="heading">ULAŞIM ve KONAKLAMA REZERVASYONU</div>

<?php					if($transportation_on_off) { ?>
						<div id="div_transportation">
							<div style="height: 20px;"></div>
							<div class="subheading">Ulaşım Bilgileri</div>
							<div style="height: 10px;"></div>

							<div style="display: flex;">
								<div align="center" style="width: 49%;">
									<div align="left" class="lbl_norm2" style="height: 17px; padding-left: 5px; padding-top: 2px; border: solid 1px green; background-color: green; color: white;">Gidiş</div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div align="left" class="lbl_norm2" style="height: 17px; padding-left: 5px; padding-top: 2px; border: solid 1px red; background-color: red; color: white;">Dönüş</div>
								</div>
							</div>
							<div style="display: flex;">
								<div align="center" style="width: 49%;">
									<div style="height: 100%; border: solid 1px green;">
										<div style="height: 12px;"></div>
										<div align="left" style="width: 90%;">
											<div class="databox2" style="height: 1px;">
												<div class="databox_label2" style="border-left: none; border-right: none; padding: 0px;"></div>
												<div class="databox_value2" style="border-left: none; border-right: none; padding: 0px;"></div>
											</div>
<?php										if($departure['transportation_mode']) { ?>
											<div class="databox2">
												<div class="databox_label2">Ulaşım Yöntemi:</div>
												<div class="databox_value2"><?php echo $departure['transportation_mode']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['port']) { ?>
											<div class="databox2">
												<div class="databox_label2">Kalkış Yeri:</div>
												<div class="databox_value2"><?php echo $departure['port']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['company']) { ?>
											<div class="databox2">
												<div class="databox_label2">Seyahat Firması:</div>
												<div class="databox_value2"><?php echo $departure['company']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['pnr_code']) { ?>
											<div class="databox2">
												<div class="databox_label2">PNR Kodu:</div>
												<div class="databox_value2"><?php echo $departure['pnr_code']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['ticket_number']) { ?>
											<div class="databox2">
												<div class="databox_label2">Bilet Numarası:</div>
												<div class="databox_value2"><?php echo $departure['ticket_number']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['ticket_price']) { ?>
											<div class="databox2">
												<div class="databox_label2">Fiyat Bilgisi:</div>
												<div class="databox_value2"><?php echo $departure['ticket_price']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['car_license_plate']) { ?>
											<div class="databox2">
												<div class="databox_label2">Araç Plakası:</div>
												<div class="databox_value2"><?php echo $departure['car_license_plate']; ?></div>
											</div>
<?php										} ?>
<?php										if($departure['date']) { ?>
											<div class="databox2">
												<div class="databox_label2">Gidiş Tarihi:</div>
												<div class="databox_value2"><?php echo $departure['date'] ? date('d.m.Y H:i', strtotime($departure['date'])) : ''; ?></div>
											</div>
<?php										} ?>
										</div>
										<div style="height: 12px;"></div>
									</div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div style="height: 100%; border: solid 1px red;">
										<div style="height: 12px;"></div>
										<div align="left" style="width: 90%;">
											<div class="databox2" style="height: 1px;">
												<div class="databox_label2" style="border-left: none; border-right: none; padding: 0px;"></div>
												<div class="databox_value2" style="border-left: none; border-right: none; padding: 0px;"></div>
											</div>
<?php										if($return['transportation_mode']) { ?>
											<div class="databox2">
												<div class="databox_label2">Ulaşım Yöntemi:</div>
												<div class="databox_value2"><?php echo $return['transportation_mode']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['port']) { ?>
											<div class="databox2">
												<div class="databox_label2">Kalkış Yeri:</div>
												<div class="databox_value2"><?php echo $return['port']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['company']) { ?>
											<div class="databox2">
												<div class="databox_label2">Seyahat Firması:</div>
												<div class="databox_value2"><?php echo $return['company']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['pnr_code']) { ?>
											<div class="databox2">
												<div class="databox_label2">PNR Kodu:</div>
												<div class="databox_value2"><?php echo $return['pnr_code']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['ticket_number']) { ?>
											<div class="databox2">
												<div class="databox_label2">Bilet Numarası:</div>
												<div class="databox_value2"><?php echo $return['ticket_number']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['ticket_price']) { ?>
											<div class="databox2">
												<div class="databox_label2">Fiyat Bilgisi:</div>
												<div class="databox_value2"><?php echo $return['ticket_price']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['car_license_plate']) { ?>
											<div class="databox2">
												<div class="databox_label2">Araç Plakası:</div>
												<div class="databox_value2"><?php echo $return['car_license_plate']; ?></div>
											</div>
<?php										} ?>
<?php										if($return['date']) { ?>
											<div class="databox2">
												<div class="databox_label2">Dönüş Tarihi:</div>
												<div class="databox_value2"><?php echo $return['date'] ? date('d.m.Y H:i', strtotime($return['date'])) : ''; ?></div>
											</div>
<?php										} ?>
										</div>
										<div style="height: 12px;"></div>
									</div>
								</div>
							</div>
						</div>
<?php					} ?>
<?php					if($accommodation_on_off) { ?>
						<div id="div_accommodation">
							<div style="height: 20px;"></div>
							<div align="left" class="blue_line">Konaklama Bilgileri</div>
							<div style="height: 10px;"></div>
							<div align="left" class="lbl_norm2" style="height: 17px; padding-left: 5px; padding-top: 2px; border: solid 1px blue; background-color: blue; color: white;">Otel</div>
							<div style="display: flex; border: solid 1px blue;">
								<div align="center" style="width: 49%;">
									<div style="height: 12px;"></div>
									<div align="left" style="width: 90%;">
										<div class="databox2" style="height: 1px;">
											<div class="databox_label2" style="border-left: none; border-right: none; padding: 0px;"></div>
											<div class="databox_value2" style="border-left: none; border-right: none; padding: 0px;"></div>
										</div>
										<div class="databox2">
											<div class="databox_label2">Giriş Tarihi:</div>
											<div class="databox_value2"><?php echo $accommodation_info['check-in_date'] ? date('d.m.Y H:i', strtotime($accommodation_info['check-in_date'])) : ''; ?></div>
										</div>
										<div class="databox2">
											<div class="databox_label2">Adı:</div>
											<div class="databox_value2"><?php echo $accommodation_info['hotel_name']; ?></div>
										</div>
									</div>
									<div style="height: 12px;"></div>
								</div>
								<div style="width: 2%;"></div>
								<div align="center" style="width: 49%;">
									<div style="height: 12px;"></div>
									<div align="left" style="width: 90%;">
										<div class="databox2" style="height: 1px;">
											<div class="databox_label2" style="border-left: none; border-right: none; padding: 0px;"></div>
											<div class="databox_value2" style="border-left: none; border-right: none; padding: 0px;"></div>
										</div>
										<div class="databox2">
											<div class="databox_label2">Çıkış Tarihi:</div>
											<div class="databox_value2"><?php echo $accommodation_info['check-out_date'] ? date('d.m.Y H:i', strtotime($accommodation_info['check-out_date'])) : ''; ?></div>
										</div>
									</div>
									<div style="height: 12px;"></div>
								</div>
							</div>
						</div>
<?php					} ?>
						

						
						

						<div style="height: 10px;"></div>
						<div id="div_save_buttons" align="right">
							<div style="display: flex; width: 300px;">
								<div hidden>
									<input type="button" id="btn_next" class="btn" value="İleri ⮞" onClick="toggle_visibility([$('#div_approval_buttons')], [$('#div_save_buttons')]);" />
								</div>
								<div style="width: 15px;" hidden></div>
								<div>
									<input type="button" id="btn_cancel" class="btn" value="İptal ✘" onClick="cancel_reservation();" />
								</div>
								<div style="width: 15px;"></div>
								<div>
									<input type="button" id="btn_edit" class="btn" value="Düzenle ✎" onClick="toggle_visibility([$('#div_form_page')], [$('#div_approve_page')]);" />
								</div>
								<div style="width: 15px;"></div>
								<div>
									<input type="button" id="btn_approve" class="btn" value="Onayla ✔" onClick="save_reservation();" />
								</div>
							</div>
						</div>
						<div id="div_approval_buttons" align="center" hidden>
							<div style="display: flex; width: 300px;">
								<div hidden>
									<input type="button" id="btn_back" class="btn" value="Geri ⮜" onClick="toggle_visibility([$('#div_save_buttons')], [$('#div_approval_buttons')]);" />
								</div>
								<div style="width: 15px;" hidden></div>
								<div>
									<a href="<?php echo $reject_link; ?>" target="_blank"><input type="button" id="btn_reject" class="btn_red" value="Reddet ✘" /></a>
								</div>
								<div style="width: 15px;"></div>
								<div>
									<a href="<?php echo $revise_link; ?>" target="_blank"><input type="button" id="btn_revise" class="btn_yellow" value="Revize ✎" /></a>
								</div>
								<div style="width: 15px;"></div>
								<div>
									<a href="<?php echo $approve_link; ?>" target="_blank"><input type="button" id="btn_approve" class="btn_green" value="Onayla ✔" /></a>
								</div>
							</div>
						</div>
						<div style="height: 22px;"></div>
					</div>
					<div id="div_completion_page" hidden>
						<div style="height: 40px;"></div>
						<div class="heading">ULAŞIM ve KONAKLAMA REZERVASYONU</div>
						<div style="height: 30px;"></div>
						<div align="center">
							<div><img src="images/ok.png" width="50" /></div>
							<div style="height: 20px;"></div>
							<div>Ulaşım ve Konaklama Rezervasyonu oluşturuldu.</div>
							<div style="height: 20px;"></div>
							<div style="display: flex; width: 90px;">
								<div hidden>
									<input type="button" id="btn_back2" class="btn" value="Geri ⮜" onClick="toggle_visibility([$('#div_preview_page')], [$('#div_completion_page')]);" />
								</div>
								<div style="width: 15px;" hidden></div>
								<div>
									<input type="button" id="btn_ok" class="btn" value="Tamam ✔" onClick="window.open('./reservation_entry_form.php', '_self');" />
								</div>
							</div>
						</div>
						<div style="height: 40px;"></div>
					</div>
				