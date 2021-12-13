<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:1450px;">

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div style="padding:16px">
                        <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                        <label>출퇴근 조회</label>
                    </div>

                    <button class="filletbutton" type="button" onclick="location.href='/fm/download_attendance/<?= $this_team ?>'" style="margin-right:16px;">엑셀로 저장</button>

                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <div style="padding:16px 16px 20px 16px;">
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

                        <?php if(count($attendance_dates) > 0) : ?>
                            <div>
                                <button id="team_delete" class="filletbutton" type="button">팀삭제</button>
                            </div>
                        <?php endif ?>
                    </div>
                    

                    <?php if(count($attendance_dates) > 0) : ?>

                        <div style="margin-top:24px;">※출퇴근기준시간은 오전 5시이며, 오전 5시가 지나지않으면 아직 다음날이 아닌것으로 간주됩니다.</div>

                        <div style="margin-top:8px; margin-bottom:24px">

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
                                            <td class="teammate_edit" data-tm_id="<?= $teammate['id'] ?>" data-tm_name="<?= $teammate['name'] ?>" data-tm_birthday="<?= $teammate['birthday'] ?>" style="font-weight:normal">
                                                <?= $teammate['name'] ?> ( <?= $teammate['birthday'] ?> )
                                            </td>

                                            <?php foreach($attendance_dates as $index => $date) : ?>
                                                    
                                                <?php 
                                                    $attendance_date = explode(" ", $date)[0];
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

                                                        if($record['type'] == 0) {
                                                            $day_on_record = $record;
                                                        } else if($record['type'] == 1) {
                                                            $day_off_record = $record;
                                                        }
                                                    }
                                                ?>

                                                <!-- 출근시간                   Attendance의 id, type, date     Teammate의 id, name을 data로 보낸다 -->
                                                <td class="attendance_edit" data-at_id="<?= $day_on_record['id'] ?? null ?>" data-at_id2="<?= $day_off_record['id'] ?? null ?>" data-at_type="0" data-at_date="<?= $attendance_date ?>" data-tm_id="<?= $teammate['id'] ?>" data-tm_name="<?= $teammate['name'] ?>" style="text-align:center;">
                                                    <?= !is_null($day_on_record) ? str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getHour(), 2, "0", STR_PAD_LEFT) . ":" . str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_on_record['created_at'])->getMinute(), 2, "0", STR_PAD_LEFT) : "" ?>
                                                </td>

                                                <?php if(!is_null($day_on_record) || !is_null($day_off_record)) : ?>

                                                    <!-- 퇴근시간                   Attendance의 id, type, date     Teammate의 id, name을 data로 보낸다  -->
                                                    <td class="attendance_edit" data-at_id="<?= $day_off_record['id'] ?? null ?>"  data-at_type="1" data-at_date="<?= $attendance_date ?>" data-tm_id="<?= $teammate['id'] ?>" data-tm_name="<?= $teammate['name'] ?>" style="text-align:center;">
                                                        <?= !is_null($day_off_record) ? str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getHour(), 2, "0", STR_PAD_LEFT) . ":" . str_pad(CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $day_off_record['created_at'])->getMinute(), 2, "0", STR_PAD_LEFT) : "" ?>
                                                    </td>

                                                <?php else : ?>

                                                    <td class="alert_message"></td>

                                                <?php endif ?>

                                            <?php endforeach ?>

                                        </tr>

                                    <?php endforeach ?>
                                </tbody>

                            </table>
                        </div>

                        <div style="display:flex; width:220px; margin:0 auto;">
                        
                            <div style="flex:1;">
                                <?php if(!$is_first_week) : ?>
                                    <a href="<?= route_to('view_attendance', $this_team, $attendance_dates[0]->subDays(7)->getTimestamp()) ?>">◀이전주보기</a>
                                <?php endif ?>
                            </div>

                            <div style="flex:1; text-align:right;">
                                <?php if(!$is_after) : ?>
                                    <a href="<?= route_to('view_attendance', $this_team, $attendance_dates[0]->addDays(7)->getTimestamp()) ?>">다음주보기▶</a>
                                <?php endif ?>
                            </div>

                        </div>

                        <!-- iframe id="txtArea1" style="display:none"></iframe -->

                    <?php endif ?>
            
                </div>

            </div>
        </div>
        
    </form>

    <!-- 팀삭제 modal -->
    <div id="team_delete_modal" class="ui mini modal">

        <form id="team_delete_form" class="ui form" method="POST" action="/fm/delete_team">
            <div style="padding:16px;">

                <div style="margin-top:4px; line-height:150%">해당 팀의 팀원정보와 출석기록을 모두 삭제하며 복구는 불가합니다.</div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="team_delete_submit" style="color:#5599DD; cursor:pointer;">확인</span>
                </div>

            </div>

            <input type="hidden" name="team_id" value="<?= $this_team ?>">
        </form>

    </div>

    <!-- 팀원정보 변경 modal -->
    <div id="teammate_edit_modal" class="ui mini modal">

        <form id="teammate_edit_form" class="ui form" method="POST" action="/fm/edit_teammate_info">

            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>성명</label>
                    <div id="teammate_edit_name" style="width:270px">고길동</div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label>생년월일</label>
                    <input id="teammate_edit_birthday" type="text" name="teammate_new_birthday" placeholder="생년월일 (예 740101)" style="width:270px">
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px; margin-bottom:4px">
                    <span id="teammate_delete_submit" style="color:#5599DD; cursor:pointer;">팀원 삭제</span>
                    <div class="actions">
                        <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
                        <span id="teammate_edit_submit" style="color:#5599DD; margin-left:32px; cursor:pointer;">확인</span>
                    </div>
                </div>

            </div>

            <input id="teammate_id" type="hidden" name="teammate_id">
            <input id="teammate_delete" type="hidden" name="teammate_delete">

        </form>

    </div>
    
    <!-- 출근기록수정 및 추가 modal -->
    <div id="attendance_edit_modal" class="ui mini modal">

        <form id="attendance_edit_form" class="ui form" method="POST" action="/fm/edit_attendance">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>팀원명</label>
                    <div id="text_teammate_name" style="width:270px">김팀원</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:28px">
                    <label id="label_attendance_day">출퇴근일</label>
                    <div id="text_attendance_date" style="width:270px">출퇴근일</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label id="label_attendance_time">출퇴근시</label>
                    <input type="text" name="attendance_time" placeholder="00:00 형식으로 작성" style="width:270px">
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                    <span id="attendance_delete_submit" style="color:#5599DD; cursor:pointer;">출퇴근기록 삭제</span>
                    <div class="actions" style="text-align:right;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                        <span id="attendance_edit_submit" style="color:#5599DD; cursor:pointer;">추가/수정</span>
                    </div>
                </div>

            </div>

            <input type="hidden" name="attendance_id">
            <input type="hidden" name="attendance_id2">
            <input type="hidden" name="attendance_type">
            <input type="hidden" name="attendance_date">
            <input type="hidden" name="teammate_id">
            <input type="hidden" name="is_delete">
        </form>

    </div>
    
    <!-- 퇴근시간불가 modal -->
    <div id="alert_message_modal" class="ui mini modal">

        <div style="padding:16px;">

            <div style="margin-top:4px; line-height:150%">출근시간이 없기 때문에 퇴근시간을 정할 수 없습니다.</div>
            <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                <span class="cancel" style="color:#5599DD; cursor:pointer;">닫기</span>
            </div>

        </div>

    </div>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    /* iframe을 이용하여 html을 그대로 엑셀파일로 만들기
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
    */
    
    $(document).ready(function() {
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_attendance/' + this.value;

        });

        $('#exportExcel').click(function() {

            fnExcelReport();

        });


        //작업삭제
        $('#team_delete').click(function() {

            $('#team_delete_modal').modal('show');

        });
        $('#team_delete_submit').click(function() {

            $('#team_delete_form').submit();

        });

        $('td.attendance_edit').click(function() {

            var attendance_time = $(this).text().trim();
            var atteandance_id = $(this).data('at_id');
            var atteandance_id2 = $(this).data('at_id2');
            var atteandance_type = $(this).data('at_type');
            var attendance_date = $(this).data('at_date');
            var teammate_id = $(this).data('tm_id');
            var teammate_name = $(this).data('tm_name');

            $('#text_teammate_name').text(teammate_name);
            $('#text_attendance_date').text(attendance_date);

            if(atteandance_type == 0) {
                $('#label_attendance_day').text('출근일');
                $('#label_attendance_time').text('출근시');

                if(atteandance_id2 != "") {
                    $('#attendance_delete_submit').text('출퇴근기록 삭제');
                } else {
                    $('#attendance_delete_submit').text('출근기록 삭제');
                }
                
            } else if(atteandance_type == 1) {
                $('#label_attendance_day').text('퇴근일');
                $('#label_attendance_time').text('퇴근시');
                $('#attendance_delete_submit').text('퇴근기록 삭제');
            }
            
            if(attendance_time == ""){
                $('#attendance_delete_submit').css('visibility', 'hidden');
                $('#attendance_edit_submit').text('추가');
            } else {
                $('#attendance_delete_submit').css('visibility', 'visible');
                $('#attendance_edit_submit').text('수정');
            }
            
            $('#attendance_edit_form [name=attendance_time]').val(attendance_time);

            $('#attendance_edit_form [name=attendance_id]').val(atteandance_id);
            $('#attendance_edit_form [name=attendance_id2]').val(atteandance_id2);
            $('#attendance_edit_form [name=attendance_type]').val(atteandance_type);
            $('#attendance_edit_form [name=attendance_date]').val(attendance_date);
            $('#attendance_edit_form [name=teammate_id]').val(teammate_id);

            
            $('#attendance_edit_modal').modal('show');

        });
        $('#attendance_edit_submit').click(function() {
            
            $('#attendance_edit_form [name=is_delete]').val(false);
            $('#attendance_edit_form').submit();

        });
        $('#attendance_delete_submit').click(function() {

            $('#attendance_edit_form [name=is_delete]').val(true);
            $('#attendance_edit_form').submit();

        });

        $('td.alert_message').click(function() {

            $('#alert_message_modal').modal('show');

        });
    
        $('td.teammate_edit').click(function() {

            var teammate_id = $(this).data('tm_id');
            var teammate_name = $(this).data('tm_name');
            var teammate_birthday = $(this).data('tm_birthday');

            $('#teammate_id').val(teammate_id);
            $('#teammate_edit_name').text(teammate_name);
            $('#teammate_edit_birthday').val(teammate_birthday);
            
            $('#teammate_edit_modal').modal('show');

        });
        $('#teammate_edit_submit').click(function() {

            $('#teammate_edit_form').submit();

        });
        $('#teammate_delete_submit').click(function() {

            $('#teammate_edit_form [name=teammate_delete]').val("true");
            $('#teammate_edit_form').submit();

        });

    });

</script>

<?= $this->endSection() ?>