<!DOCTYPE html>

<html>


    <head>
    
        <!-- You MUST include jQuery before Fomantic -->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.3.1/dist/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.css">
        <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.8.8/dist/semantic.min.js"></script>



        <style>
            
            div {
                padding-top: 4px;
                padding-bottom: 4px;
            }
            .uiframe {
                padding-top: 8px;
                padding-bottom: 8px;
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
            label {
                color: #666666;
                font-weight: bold;
            }
            .bluelabel {
                color: #5599DD;
            }
            .excel {
                width: 100%;
                color: black;
                background-color: #FFFFFF;
                border-collapse: collapse;
            }
            .excel td {
                border: 1px solid #AAAAAA;
            }
            select {
                outline: none;
            }

            .dropbtn {
                background-color: #04AA6D;
                color: white;
                padding: 16px;
                font-size: 16px;
                border: none;
            }

            /* The container <div> - needed to position the dropdown content */
            .dropdown {
                position: relative;
                display: inline-block;
            }

            /* Dropdown Content (Hidden by Default) */
            .dropdown-content {
                display: none;
                position: absolute;
                background-color: #f1f1f1;
                min-width: 160px;
                box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
                z-index: 1;
            }

            /* Links inside the dropdown */
            .dropdown-content a {
                color: black;
                padding: 12px 16px;
                text-decoration: none;
                display: block;
            }

                /* Change color of dropdown links on hover */
            .dropdown-content a:hover {background-color: #ddd;}

                /* Show the dropdown menu on hover */
            .dropdown:hover .dropdown-content {display: block;}

                /* Change the background color of the dropdown button when the dropdown content is shown */
            .dropdown:hover .dropbtn {background-color: #3e8e41;}


        </style>

    </head>

    <body>

        <div class="ui form" style="width:100%; padding-top:16px">

            <?= $this->renderSection('content') ?>

        </div>

        <script type="text/javascript">

            $('#standard_calendar').calendar({
                startMode: 'year',
                type: 'date', 
                text: {
                    months: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                    monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                }
            });

            $('.ui.dropdown').dropdown();

            <?php $alert = session()->getFlashdata('alert'); ?>

            <?php if(!is_null($alert)) : ?>
                
            $(document).ready(function() {
                alert("<?= $alert ?>");
            });
            
            <?php endif ?>

        </script>

        <?= $this->renderSection('custom_js') ?>
        
    </body>

</html>