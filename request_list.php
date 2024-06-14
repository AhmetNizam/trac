<?php
	require("./library.php");

	$list_type = $_GET['list_type'] ?? '1';
	$status = $_GET['status'] ?? '0';
?>
<!doctype HTML 4.01 Transitional>
<html xmlns="http://www.w3.org/2001/XMLSchema">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=750">

		<title>Ulaşım ve Konaklama - Talep Listesi</title>

		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.13.3.css" />
		<link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

		<script src="./js/jquery-3.7.1.js"></script>
		<script src="./js/jquery-ui-1.13.3.js"></script>

		<script src="./js/jquery.inputmask.min.js"></script>
		<script src="./js/inputmask.binding.js"></script>

		<script type="text/javascript">

			$(document).ready(function() {

				//$('#inp_birthdate').inputmask("gg.aa.yyyy");
				$('#inp_identityno').inputmask("99999999999");
				$('#inp_phone').inputmask("9(999) 999 99 99");

				$(window).on('resize', function(event) {
					$('#div_main_area').css('margin-left', ($(window).width() - $('#div_main_area').width()) / 2);
				});

				$(window).on('scroll', function(event) {
					if(window.scrollY > 0) {
						$('#div_up_slider').show();
					} else {
						$('#div_up_slider').hide();
					}
				});

				set_status();
			});

			function gototop() {
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			}

			function toggle(direction) {
				if(direction == 'expand') {
					$('div[id*="div_request_"]').slideDown('slow');
				} else if(direction == 'collapse') {
					$('div[id*="div_request_"]').slideUp('slow');
				}
				gototop();
			}

			function set_status(status_id = $('#sel_status').val(), start_date = $('#inp_start_date').val(), end_date = $('#inp_end_date').val()) {
				if(status_id != '') {
					$('#div_requests').load('./get_request_list.php?list_type=' + <?php echo $list_type; ?> + '&status_id=' + status_id + '&start_date=' + start_date + '&end_date=' + end_date);
				}
			}

			function slide_request(item) {
				item.slideToggle('slow');
				$('html, body').animate({ scrollTop: item.parent().offset().top }, 'slow');
			}

			function slide_manager_process(item1, item2) {
				item1.slideUp('slow');
				item2.slideDown('slow');
				$('html, body').animate({ scrollTop: item1.parent().parent().offset().top }, 'slow');
			}

		</script>
	</head>
	<body>
		<div style="position: fixed; display: flex; justify-content: right; width: 490px; height: 30px; top: 17px; left: 50vw;">
			<div style="width: 30px;">
				<div id="div_up_slider1" style="height: 30px; background: url('./images/top-icon.png'); background-size: cover;" title="Sayfa Başına Git" onClick="gototop();"></div>
				<div id="div_up_slider2" style="height: 30px; background: url('./images/expand-icon.png'); background-size: cover;" title="Genişlet" onClick="toggle('expand');"></div>
				<div id="div_up_slider3" style="height: 30px; background: url('./images/collapse-icon.png'); background-size: cover;" title="Daralt" onClick="toggle('collapse');"></div>
			</div>
		</div>
		<div id="div_main_area" hidden>
			<div id="div_main_frame" class="main_frame">
				<div style="height: 40px;"></div>
				<div class="heading">ULAŞIM ve KONAKLAMA TALEP LİSTESİ</div>
				<div style="height: 15px;"></div>
				<div style="display: flex;">
					<div align="left" style="width:50%;">
						<div style="display: flex; width: 90%; height: 38px;">
							<div style="width: 35%;">
								<div id="div_lbl0" class="lbl_norm1" alert="lbl_alrt1">Başlangıç:</div>
							</div>
							<div style="width: 65%;">
								<input type="date" id="inp_start_date" name="inp_start_date" class="inp" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 6, date('Y')));; ?>" onChange="set_status();" />
							</div>
						</div>
						<div style="display: flex; width: 90%; height: 38px;">
							<div style="width: 35%;">
								<div id="div_lbl0" class="lbl_norm1" alert="lbl_alrt1">Bitiş:</div>
							</div>
							<div style="width: 65%;">
								<input type="date" id="inp_end_date" name="inp_end_date" class="inp" value="<?php echo date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')));; ?>" onChange="set_status();" />
							</div>
						</div>
					</div>
					<div align="right" style="width:50%;">
						<div style="display: flex; width: 90%; height: 38px;">
							<div style="width: 35%;">
								<div align="left" id="div_lbl0" class="lbl_norm1" alert="lbl_alrt1">Talep Durumu:</div>
							</div>
							<div style="width: 65%;">
								<select id="sel_status" name="sel_status" label="div_lbl0" area="area1" action="go" class="inp" onChange="set_status();">
									<option value="">-- Seçiniz --</option>
									<option value="0" <?php if($status == '0') { echo 'selected'; } ?>>Tümü</option>
									<option value="1" <?php if($status == '1') { echo 'selected'; } ?>>Bekliyor</option>
									<option value="2" <?php if($status == '2') { echo 'selected'; } ?>>Onaylandı</option>
									<option value="3" <?php if($status == '3') { echo 'selected'; } ?>>Rezervasyon Yapıldı</option>
									<option value="4" <?php if($status == '4') { echo 'selected'; } ?>>Revize Talep Edildi</option>
									<option value="5" <?php if($status == '5') { echo 'selected'; } ?>>Reddedildi</option>
								</select>
							</div>
						</div>
						<div style="display: flex; width: 90%; height: 38px;">
							<div style="width: 49%;"><input type="button" class="inp" value="Genişlet" onClick="toggle('expand');" /></div>
							<div style="width: 2%;"></div>
							<div style="width: 49%;"><input type="button" class="inp" value="Daralt" onClick="toggle('collapse');" /></div>
						</div>
					</div>
				</div>
				<div style="height: 15px;"></div>
			</div>
			<div style="height: 5px;"></div>
			<div id="div_requests"></div>
		</div>
	</body>
	<script type="text/javascript">
		window.onload = function() {
			$('#div_main_area').css('margin-left', ($(window).width() - $('#div_main_area').width()) / 2);
			$('#div_main_area').show();
		};
	</script>
</html>

