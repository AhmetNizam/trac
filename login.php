<?php
	session_start();
	session_unset();
?>
<!doctype HTML 4.01 Transitional>
<html xmlns="http://www.w3.org/2001/XMLSchema">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Ulaşım ve Konaklama - Giriş</title>

    <link rel="stylesheet" type="text/css" href="./css/main.css" />
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.13.3.css" />
    <link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

    <script src="./js/jquery-3.7.1.js"></script>
    <script src="./js/jquery-ui-1.13.3.js"></script>

    <script src="./js/jquery.inputmask.min.js"></script>
    <script src="./js/inputmask.binding.js"></script>

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
                                window.location.assign('./index.php');
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
<body topmargin="20" style="background: url('./images/body_gray.png'); background-color: whitesmoke;">
    <table border="0" cellspacing="0" cellpadding="0" style="width: 100%; height: 100%;">
        <tr>
            <td align="center">
                <form id="form1" method="post" enctype="multipart/form-data" action="">
                    <div style="width: 350px; height: 200px;">
                        <div align="center" class="main_frame" style="background: url('./images/body.png'); background-color: seashell; width: 300px;">
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
                                <input type="button" id="btn_login" class="btn" value="Giriş" onClick="login();" />
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