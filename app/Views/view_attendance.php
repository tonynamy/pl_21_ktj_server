<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:1450px;">

            <div style="padding:16px">
                <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                <label>출퇴근 조회</label>
            </div>

            <div style="height:1px; background-color:#e8e9e9;"></div>

            <div style="padding:16px">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="width:330px; display:flex; align-items:center;">
                        <label style="width:80px">팀선택</label>
                        <select id="team_select" class="ui dropdown" name="team">
                            <option value="">팀이름</option>

                            <?php foreach($teams as $team) : ?>

                            <option value="<?=$team['id']?>" <?= $team['id'] == $this_team ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                    <button class="filletbutton" type="submit" formaction="logout">출근기록 넣기</button>
                </div>
                

                <?php if(count($attendance_dates) > 0) : ?>

                    <div style="margin-top:16px; margin-bottom:8px">

                        <table class="ui selectable compact celled definition table">

                            <thead class="full-width">
                                <tr>
                                    <th height="34px" style="font-weight:normal; padding-top:0px; padding-bottom:0px"> <?= $attendance_dates[0]->getYear() ?>년 </th>

                                    <?php foreach($attendance_dates as $date) : ?>

                                        <th class="center aligned" width="90px" style="font-weight:normal; padding-top:0px; padding-bottom:0px"> <?= $date->getMonth() ?>/<?= $date->getDay() ?> 출근</th>
                                        <th class="center aligned" width="90px" style="font-weight:normal; padding-top:0px; padding-bottom:0px"> <?= $date->getMonth() ?>/<?= $date->getDay() ?> 퇴근</th>
                                            
                                    <?php endforeach ?>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach($attendance_teammates as $teammate) : ?>

                                    <tr>

                                        <td class="edit_teammate_info" style="font-weight:normal">
                                            <?= $teammate['name'] ?> ( <?= $teammate['birthday'] ?> )
                                        </td>

                                        <?php foreach($attendance_dates as $index => $date) : ?>
                                                
                                            <?php 
                                                $attendance_record = array_filter($attendance_data[$index], function($data) use ($date, $teammate) {

                                                    $attendance_created_at = CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $data['created_at']);
                                                    $start_time = $date->setHour(5)->setMinute(0)->setSecond(0);
                                                    $end_time = $start_time->addDays(7);
                                                    return $data['teammate_name'] == $teammate['name'] && $data['teammate_birthday'] == $teammate['birthday'] &&
                                                    $attendance_created_at >= $start_time && $attendance_created_at < $end_time;
                                                });

                                                $day_on_record = null;
                                                $day_off_record = null;

                                                foreach($attendance_record as $record) {

                                                    if($record['type'] == 0) $day_on_record = $record;
                                                    else if($record['type'] == 1) $day_off_record = $record;
                                                }
                                            ?>

                                            <?php if(!is_null($day_on_record)) : ?>

                                                <td class="center aligned"> <?= str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getHour(), 2, "0", STR_PAD_LEFT) ?>:<?= str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getMinute(), 2, "0", STR_PAD_LEFT) ?> </td>

                                            <?php else : ?>

                                                <td></td>

                                            <?php endif ?>

                                            <?php if(!is_null($day_off_record)) : ?>

                                                <td class="center aligned"> <?= str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getHour(), 2, "0", STR_PAD_LEFT) ?>:<?= str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getMinute(), 2, "0", STR_PAD_LEFT) ?> </td>

                                            <?php else : ?>

                                                <td></td>

                                            <?php endif ?>

                                        <?php endforeach ?>

                                    </tr>

                                <?php endforeach ?>
                            </tbody>

                        </table>
                    </div>

                    <div style="margin:0 auto; width:fit-content">
                        <a href="<?= route_to('view_attendance', $this_team, $attendance_dates[0]->subDays(7)->getTimestamp()) ?>" style="margin-right:50px">◀이전주보기</a>
                        <a href="<?= route_to('view_attendance', $this_team, $attendance_dates[0]->addDays(7)->getTimestamp()) ?>">다음주보기▶</a>
                    </div>
                        
                    <div align="right">
                        <button class="bluebutton" id="exportExcel" type="button" style="width:130px">엑셀로 저장</button>
                    </div>

                    <iframe id="txtArea1" style="display:none"></iframe>

                <?php endif ?>
        
            </div>

        </div>
    </div>
    
</form>

<div id="edit_teammate_info_modal" class="ui mini modal" style="padding:16px;">

    <div class="ui input" style="display:flex; align-items:center; margin-bottom:8px;">
        <label style="width:70px">아이디</label>
        <input type="text" name="name" placeholder="이름" value="">
    </div>

    <div class="ui input" style="display:flex; align-items:center; margin-bottom:24px;">
        <label style="width:70px">패스워드</label>
        <input type="text" name="birthday" placeholder="생년월일 (예 740101)" value="">
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center;">
        <span style="color:#5599DD; cursor:pointer;">팀원 삭제</span>
        <div class="actions">
            <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
            <span style="color:#5599DD; margin-left:32px; cursor:pointer;">확인</span>
        </div>
    </div>

    <form id="edit_user_info_form" class="ui form" method="POST" action="">
        <input id="edit_teammate_info_name" type="hidden" name="teammate_name" />
        <input id="edit_teammate_info_birthday" type="hidden" name="teammate_birthday" />
    </form>

</div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    function fnExcelReport() {
        var t = $('table.ui.table').clone();

        t.find('a.item').remove();

        tab_text = t.wrap('<p>').parent().html();

        tab_text= tab_text.replace(/<A[^>]*>|<\/A>/g, ""); //remove if u want links in your table
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

            location.href = '/fm/view_attendance/' + this.value;

        });

        $('#exportExcel').click(function() {

            fnExcelReport();

        });

        /*
        $('.item.name_change').click(function() {

            var id = $(this).data('id');

            var new_name = prompt('바꿀 이름을 입력하세요.');

            if(new_name != null) {

                var newForm = $('<form></form>');

                newForm.attr("name", "newForm");
                newForm.attr("method", "post");
                newForm.attr("action", "/fm/change_teammate_name");
                
                newForm.append($('<input/>', {type: 'hidden', name: 'teammate_id', value: id }));
                newForm.append($('<input/>', {type: 'hidden', name: 'new_name', value: new_name }));

                newForm.appendTo('body');
                newForm.submit();
            }
        });

        $('.item.birthday_change').click(function() {

            var id = $(this).data('id');

            var new_birthday = prompt('바꿀 생년월일을 입력하세요.');

            if(new_birthday != null) {

                var newForm = $('<form></form>');

                newForm.attr("name", "newForm");
                newForm.attr("method", "post");
                newForm.attr("action", "/fm/change_teammate_birthday");
                
                newForm.append($('<input/>', {type: 'hidden', name: 'teammate_id', value: id }));
                newForm.append($('<input/>', {type: 'hidden', name: 'new_birthday', value: new_birthday }));

                newForm.appendTo('body');
                newForm.submit();
            }
        });
        */

        
        $('td.edit_teammate_info').click(function() {

            $('#edit_teammate_info_modal').modal('show');

        });

    });

</script>

<?= $this->endSection() ?>