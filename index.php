<?php
	require("./library.php");
//	header("Location: ./login.php");
?>
<!doctype HTML 4.01 Transitional>
<html xmlns="http://www.w3.org/2001/XMLSchema">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=750">

    <title>Ulaşım ve Konaklama - Rezervasyon</title>

    <link rel="stylesheet" type="text/css" href="./css/main.css" />
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-1.13.3.css" />
    <link rel="stylesheet" type="text/css" href="./css/easy-loading.css" />

    <script src="./js/jquery-3.7.1.js"></script>
    <script src="./js/jquery-ui-1.13.3.js"></script>

    <script src="./js/jquery.inputmask.min.js"></script>
    <script src="./js/inputmask.binding.js"></script>

    <script type="text/javascript">

        $(document).ready(function() {

            $('input[type="button"]').on('mouseover', function(event) {
                let div = $(event.target).parent().parent().children('div[id*="div_menu_arrow"]');
                if(div.attr('class') == 'menu_arrow_pasive') {
                    div.show();
                }
            });

            $('input[type="button"]').on('mouseleave', function(event) {
                let div = $(event.target).parent().parent().children('div[id*="div_menu_arrow"]');
                if(div.attr('class') == 'menu_arrow_pasive') {
                    div.hide();
                }
            });

        });

        function load_page(item, iframe, url) {
            if(iframe != '' && url != '') {
                iframe = $('#' + iframe);
                $('#inp_active_frame').val(iframe.attr('id'));

                $('input[type="button"]').attr('style', 'width: 100%;');
                $('div[id*="div_menu_arrow"]').attr('class', 'menu_arrow_pasive');
                $('div[id*="div_menu_arrow"]').hide();

                item.attr('style', 'width: 100%; border-color: red;	outline: solid 1px red; color: white; background-color: #008FAE;');
                item.parent().parent().children('div[id*="div_menu_arrow"]').attr('class', 'menu_arrow_active');
                item.parent().parent().children('div[id*="div_menu_arrow"]').show();
            } else {
                iframe = $('#' + $('#inp_active_frame').val());
            }

            $.each($('iframe[id*="iframe_page"]'),
                function() {
                    if(this.contentWindow.scrollX > 0) {
                        $(this).data('scrollLeft', this.contentWindow.scrollX);
                    }
                    if(this.contentWindow.scrollY > 0) {
                        $(this).data('scrollTop', this.contentWindow.scrollY);
                    }
                }
            );

            $('div[id*="div_frame"]').hide();
            iframe.parent().show();

            if(iframe.attr('src') == '') {
                iframe.attr('src', url);
            } else {
                let scrollLeft = iframe.data('scrollLeft') || 0;
                let scrollTop = iframe.data('scrollTop') || 0;

                iframe[0].contentWindow.scrollTo(scrollLeft, scrollTop);
            }
        }

    </script>
</head>
<body>
    <input type="hidden" id="inp_active_frame" value="" />
    <div style="width: 100%; height: 100%; display: flex;">
        <div style="width: 20%; height: 100%; border-right: solid 1px blue;">
            <div style="height: 15px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_blue" style="width: 100%;" value="Yeni Talep Oluştur" onClick="load_page($(this), 'iframe_page1', './request_entry_form.php');" /></div>
                <div id="div_menu_arrow1" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_blue" style="width: 100%;" value="Taleplerim" onClick="load_page($(this), 'iframe_page2', './request_list.php?list_type=1');" /></div>
                <div id="div_menu_arrow2" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_blue" style="width: 100%;" value="Rezervasyonlarım" onClick="load_page($(this), 'iframe_page3', './reservation_list.php?list_type=1');" /></div>
                <div id="div_menu_arrow3" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 20px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%; border-bottom: solid 2px blue;"></div>
                <div style="width: 10%;"></div>
            </div>
            <div style="height: 10px;"></div>
<?php	if($_SESSION['executive_person']) { ?>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_aquamarine" style="width: 100%;" value="Yeni Rezervasyon Oluştur" onClick="load_page($(this), 'iframe_page4', './reservation_entry_form.php');" /></div>
                <div id="div_menu_arrow4" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
<?php	} ?>
<?php	if($_SESSION['authorize_person']) { ?>

            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_aquamarine" style="width: 100%;" value="Onay Bekleyen Talepler" onClick="load_page($(this), 'iframe_page5', './request_list.php?list_type=2&status=11');" /></div>
                <div id="div_menu_arrow5" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_aquamarine" style="width: 100%;" value="Rezervasyon Bekleyen Talepler" onClick="load_page($(this), 'iframe_page6', './request_list.php?list_type=2&status=13');" /></div>
                <div id="div_menu_arrow6" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_aquamarine" style="width: 100%;" value="Talepler" onClick="load_page($(this), 'iframe_page7', './request_list.php?list_type=2');" /></div>
                <div id="div_menu_arrow7" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%;"><input type="button" class="btn_aquamarine" style="width: 100%;" value="Rezervasyonlar" onClick="load_page($(this), 'iframe_page8', './reservation_list.php?list_type=2');" /></div>
                <div id="div_menu_arrow8" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
            <div style="height: 20px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%; border-bottom: solid 2px blue;"></div>
                <div style="width: 10%;"></div>
            </div>
            <div style="height: 10px;"></div>
<?php	} ?>
            <div style="height: 10px;"></div>
            <div style="width: 100%; display: flex;">
                <div style="width: 10%;"></div>
                <div style="width: 80%"><input type="button" class="btn_red" style="width: 100%;" value="Çıkış" onClick="load_page($(this), 'iframe_page9', './login.php');" /></div>
                <div id="div_menu_arrow9" class="menu_arrow_pasive" style="width: 10%;">⮞</div>
            </div>
        </div>
        <div id="div_frame" class="menu_frame">
            <iframe id="iframe_page" src=""></iframe>
        </div>
        <div id="div_frame1" class="menu_frame">
            <iframe id="iframe_page1" src=""></iframe>
        </div>
        <div id="div_frame2" class="menu_frame">
            <iframe id="iframe_page2" src=""></iframe>
        </div>
        <div id="div_frame3" class="menu_frame">
            <iframe id="iframe_page3" src=""></iframe>
        </div>
        <div id="div_frame4" class="menu_frame">
            <iframe id="iframe_page4" src=""></iframe>
        </div>
        <div id="div_frame5" class="menu_frame">
            <iframe id="iframe_page5" src=""></iframe>
        </div>
        <div id="div_frame6" class="menu_frame">
            <iframe id="iframe_page6" src=""></iframe>
        </div>
        <div id="div_frame7" class="menu_frame">
            <iframe id="iframe_page7" src=""></iframe>
        </div>
        <div id="div_frame8" class="menu_frame">
            <iframe id="iframe_page8" src=""></iframe>
        </div>
        <div id="div_frame9" class="menu_frame">
            <iframe id="iframe_page9" src=""></iframe>
        </div>
    </div>
</body>
</html>