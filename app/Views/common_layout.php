<!DOCTYPE html>

<html>

    <head>

        <title>FMENC 비계관리 웹서비스</title>
        <link rel="shortcut icon" href="/static/favicon.ico" type="image/x-icon">
        <link rel="icon" href="/static/favicon.ico" type="image/x-icon">
        <meta property="og:type" content="website">
        <meta property="og:url" content="http://49.247.24.170/fm">
        <meta property="og:title" content="FMENC 비계관리 웹서비스">
        <meta property="og:description" content="FMENC 비계관리를 위한 웹서비스 페이지입니다.">
        <meta property="og:image" content="/static/fmenc_thumbnail.jpg">


        <!-- You MUST include jQuery before Fomantic -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.js"></script>
        <script src="/static/tablesort.js"></script>


        <style>
            .uiframe {
                padding-top: 16px;
                padding-bottom: 16px;
                padding-left: 16px;
                padding-right: 16px;
                border-radius: 8px;
                box-shadow: 0px 3px 8px #BBBBBB;
            }
            .uigray {
                padding: 16px;
                border-radius: 8px;
                background-color: #DDDDDD;
            }
            button {
                padding: 12px;
                margin-right: 8px;
                color: #555555;
                font-weight: bold;
                text-align: center;
                background: #DDDDDD;
                border-radius: 4px;
                border: 0;
            }
            button:focus {
                outline:0;
            }
            button:hover{
                color: #222222;
                background: #CCCCCC;
                cursor: pointer;
            }
            button:active {
                color: #111111;
                background: #BBBBBB;
                cursor: pointer;
            }
            .bluebutton {
                color: #FFFFFF;
                background:#5599DD;
            }
            .bluebutton:hover {
                color: #FFFFFF;
                background:#4488DD;
            }
            .bluebutton:active {
                color: #FFFFFF;
                background:#4488BB;
            }
            .filletbutton {
                height:30px;
                padding: 0px 12px 0px 12px;
                border-radius: 15px;
            }
            label {
                color: #666666;
                font-weight: bold;
            }
            .bluelabel {
                color: #5599DD;
            }
            .normalfont {
                font-weight: normal;
            }
            .excel {
                width: 100%;
                color: black;
                background-color: #FFFFFF;
                border-collapse: collapse;
            }
            .excel td {
                font-weight: normal;
                border: 1px solid #AAAAAA;
            }

        </style>

    </head>

    <body style="height:100%; overflow-x: auto;">

        <div class="ui form" style="width:100%; height:100%;">
            <?= $this->renderSection('content') ?>
        </div>

        <script type="text/javascript">

            <?php $alert = session()->getFlashdata('alert'); ?>

            <?php if(!is_null($alert)) : ?>
                
                $(document).ready(function() {
                    alert("<?= $alert ?>");
                });
                
            <?php endif ?>

            $('.ui.dropdown').dropdown();

        </script>

        <?= $this->renderSection('custom_js') ?>
        
    </body>

</html>