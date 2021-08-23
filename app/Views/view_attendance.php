<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div class="uiframe" style="margin:0 auto; width:1300px">

        <div style="width:280px; display:flex; align-items:center">
            <label style="width:80px">팀선택</label>
            <select id="team_select" class="ui fluid dropdown" name="team">
                <option value="-1">팀이름</option>

                <?php foreach($teams as $team) : ?>

                <option value="<?=$team['id']?>" <?= $team['id'] == $this_team ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                <?php endforeach ?>
            </select>
        </div>

        <?php if(count($attendance_dates) > 0)  : ?>


            <div style="margin-top:8px; margin-bottom:8px">
                <table class="excel">
                    <tr bgcolor="#F2F2F2">

                        <td width="120px"><?=$attendance_dates[0]->getYear()?>년</td> 

                        <?php foreach($attendance_dates as $date) : ?> 

                            <td><?=$date->getMonth()?>/<?=$date->getDay()?>출근</td>
                            <td><?=$date->getMonth()?>/<?=$date->getDay()?>퇴근</td>

                        <?php endforeach ?>
                    </tr>

                    <?php foreach($attendance_teammates as $teammate) : ?>

                        <tr align="right">

                            <td bgcolor="#F2F2F2" align="left">
                                <div class="ui dropdown" style="padding:0px">
                                    <?=$teammate['name']?>(<?=$teammate['birthday']?>)
                                    <div class="menu">
                                        
                                        <a class="item name_change" data-id="<?=$teammate['id']?>" href="#">이름변경</a>
                                        <a class="item birthday_change" data-id="<?=$teammate['id']?>" href="#">생일변경</a>
                                        <a class="item team_change" data-id="<?=$teammate['id']?>" href="#">팀변경</a>
                                        <a class="item teammate_remove" data-id="<?=$teammate['id']?>" href="#">삭제</a>

                                    </div>
                                </div>
                            </td>

                            <?php foreach($attendance_dates as $index => $date) : ?> 

                                <?php 

                                    $attendance_record = array_filter($attendance_data[$index], function($data) use ($date, $teammate) {

                                        return $data['teammate_name'] == $teammate['name'] && $data['teammate_birthday'] == $teammate['birthday'] &&
                                                CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $data['created_at'])->toDateString() == $date->toDateString();

                                    });

                                    $day_on_record = null;
                                    $day_off_record = null;

                                    foreach($attendance_record as $record) {

                                        if($record['type'] == 0) $day_on_record = $record;
                                        else if ($record['type'] == 1) $day_off_record = $record;

                                    }



                                ?>

                                <?php if(!is_null($day_on_record)) : ?>

                                    <td><?=CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getHour()?>:<?=str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getMinute(), 2, "0")?></td>

                                <?php else : ?>

                                    <td></td>

                                <?php endif ?>

                                <?php if(!is_null($day_off_record)) : ?>

                                    <td><?=CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getHour()?>:<?=str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getMinute(), 2, "0")?></td>

                                <?php else : ?>

                                    <td></td>

                                <?php endif ?>

                            <?php endforeach ?>

                        </tr>

                    <?php endforeach ?>

                </table>
            </div>

            <div style="margin:0 auto; width:fit-content">
                <a href="<?=route_to('view_attendance', $this_team, $attendance_dates[0]->subDays(7)->getTimestamp())?>" style="margin-right:50px">◀이전주보기</a>
                <a href="<?=route_to('view_attendance', $this_team, $attendance_dates[0]->addDays(7)->getTimestamp())?>">다음주보기▶</a>
            </div>
            
            <div align="right">
                <button class="bluebutton" id="exportExcel" type="button" formaction="save_attendance_button" style="width:130px">엑셀로 저장</button>
            </div>

            <iframe id="txtArea1" style="display:none"></iframe>

        <?php endif ?>       

    </div>

</form>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    function fnExcelReport()
    {
        var t = $('table.excel').clone();

        t.find('a.item').remove();

        tab_text = t.wrap('<p>').parent().html();

        tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, "");//remove if u want links in your table
        tab_text= tab_text.replace(/<img[^>]*>/gi,""); // remove if u want images in your table
        tab_text= tab_text.replace(/<input[^>]*>|<\/input>/gi, ""); // reomves input params

        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE "); 

        if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))      // If Internet Explorer
        {
            txtArea1.document.open("txt/html","replace");
            txtArea1.document.write(tab_text);
            txtArea1.document.close();
            txtArea1.focus(); 
            sa=txtArea1.document.execCommand("SaveAs",true,"Say Thanks to Sumit.xls");
        }  
        else                 //other browser not tested on IE 11
            sa = window.open('data:application/vnd.ms-excel,' + encodeURIComponent(tab_text));  

        return (sa);
    }

    $(document).ready(function() {

        $('#team_select').on('change', function() {

            location.href='/fm/view_attendance/'+this.value;

        });

        $('#exportExcel').click(function() {
            
            fnExcelReport();

        });

        $('.item.name_change').click(function() {

            var id = $(this).data('id');

            var new_name = prompt('바꿀 이름을 입력하세요');

            alert(new_name+'으로 변경되었습니다.');


        });

    });


</script>


<?= $this->endSection() ?>