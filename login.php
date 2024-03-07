<?php
	session_start();
	session_unset();
?>
<html xmlns="http://www.w3.org/2001/XMLSchema">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<title>Domain User Test / Windows Authentication Test</title>

		<link rel="stylesheet" type="text/css" href="./css/main.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

		<script src="./js/jquery-3.7.1.js"></script>
		<script src="./js/jquery-ui-1.13.2/jquery-ui.js"></script>
		<script type="text/javascript">
			function login() {
				if($('#username').val() == '') {
					$('#username').attr('placeholder', 'Lütfen doldurunuz');
					$('#username').focus();
				} else if($('#password').val() == '') {
					$('#password').attr('placeholder', 'Lütfen doldurunuz');
					$('#password').focus();
				} else {
					var username = $('#username').val();
					var password = $('#password').val();

					$.getJSON('./win_auth.php?username=' + encodeURIComponent(username) + '&password=' + encodeURIComponent(password),
						function(data) {
							if(data.Rows) {
								var rowData = data.Rows[0];
								if(rowData.err_code == "0") {
									window.location.assign('./trac_inquiry_form.php');
								} else {
									$('#check_auth').html(rowData.msg);
									$('#div_error_msg').show();
								}
							}
						}
					);
				}
			}

		</script>
	</head>
	<body background="./images/body_gray.png">
		<table border="1" cellspacing="0" cellpadding="0" width="100%" height="100%">
			<tr>
				<td align="center">
					<form id="form1" name="form1" method="post" enctype="multipart/form-data" action="">
						<div style="width: 350px; height: 200px;">
							<div align="center" class="menu" style="width: 300px;">
								<div style="height: 20px;"></div>
								<div style="width: 255px;">
									<div class="style4" style="display: flex; height: 30px;">
										<div style="width: 85px; text-align: left; margin-top: 4px;">Kullanıcı Adı</div><div style="width: 12px; text-align: left; margin-top: 4px;">:</div>
										<div>
											<input type="text" id="username" name="username" class="style4" style="width:160px; height:25px; text-align-last:left;" value="" placeholder="username" />
										</div>
									</div>
									<div class="style4" style="display: flex; height: 30px;">
										<div style="width: 85px; text-align: left; margin-top: 4px;">Parola</div><div style="width: 12px; text-align: left; margin-top: 4px;">:</div>
										<div>
											<input type="password" id="password" name="password" class="style4" style="width:160px; height:25px; text-align-last:left;" value="" placeholder="password" />
										</div>
									</div>
								</div>
								<div style="height: 2px;"></div>
								<div style="width: 255px; height: 2px; border-width: 2px; border-bottom-style: solid; border-bottom-color: #CDCDCD;"></div>
								<div style="height: 8px;"></div>
								<div>
									<input type="button" id="btn2" name="btn2" value="Login" style="font-family: Century Gothic;" onClick="javascript:login();" />
								</div>
								<div style="height: 6px;"></div>
								<div id="div_error_msg" hidden>
									<div style="width: 255px; height: 2px; border-width: 2px; border-bottom-style: solid; border-bottom-color: #CDCDCD;"></div>
									<div style="width: 255px;">
										<div align="left" class="style4"><pre id="check_auth"></pre></div>
									</div>
								</div>
								<div style="height: 10px;"></div>
							</div>
						</div>
					</form>
				</td>
			</tr>
		</table>
	</body>
</html>