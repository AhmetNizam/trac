<?php
	require("./library.php");

	$approve_link = $_PARAM['webServerURL'] . '/trac/approve.php?link=' . $_SESSION['request']['uuid'] . '-' . $_SESSION['request']['approver']['uuid'];
	$revise_link = $_PARAM['webServerURL'] . '/trac/revise.php?link=' . $_SESSION['request']['uuid'] . '-' . $_SESSION['request']['approver']['uuid'];
	$reject_link = $_PARAM['webServerURL'] . '/trac/reject.php?link=' . $_SESSION['request']['uuid'] . '-' . $_SESSION['request']['approver']['uuid'];

	$request = $_SESSION['display']['request'];
	$travelers = $_SESSION['display']['travelers'];
	$list_type = '0';
	$i = 0;
?>

					<div id="div_preview_page">
<?php
	include("./request_design.php");
?>
						<div class="subheading" style="height: 2px;"></div>
						<div style="height: 10px;"></div>
						<div id="div_save_buttons" align="right">
							<div style="display: flex; width: 300px;">
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
									<a href="<?php echo $reject_link; ?>" target="_blank"><input type="button" id="btn_reject" class="btn_red" value="Reddet ✖" /></a>
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
						<div align="center">
							<div style="height: 20px;"></div>
							<div><img src="images/ok.png" width="50" /></div>
							<div style="height: 20px;"></div>
							<div>Ulaşım ve Konaklama Talebi oluşturuldu.</div>
							<div style="height: 20px;"></div>
							<div style="display: flex; width: 90px;">
								<div>
									<input type="button" id="btn_ok" class="btn" value="Tamam ✔" onClick="window.open('./request_entry_form.php', '_self');" />
								</div>
							</div>
						</div>
						<div style="height: 40px;"></div>
					</div>
				