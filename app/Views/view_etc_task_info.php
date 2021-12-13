<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">
        <input type="hidden" name="task_name" value="<?= $task_name ?>">
        <input type="hidden" name="team_id"  value="<?= $this_team['id'] ?>">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:1000px; padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center;">

                    <div style="display:flex; align-items:center;">
                        <i class="arrow left icon" onclick="location.href='/fm/view_etc_task'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                        <span style="font-size:x-large; font-weight:normal;"><?= $task_name ?> (<?= $this_team['name'] ?>)</span>
                    </div>

                    <div>
                        <button id="task_change_team" class="filletbutton" type="button" style="margin-right:4px"><?= $progress == 0 ? "작업팀변경" : "작업기록팀변경" ?></button>
                        <?php if($progress == 1) : ?>
                            <button id="task_finish" class="filletbutton" type="button" style="margin-right:4px">작업완료</button>
                        <?php endif ?>
                        <button id="task_delete" class="filletbutton" type="button">작업삭제</button>
                    </div>
                </div>

                <div style="margin-top:16px;">
                    <table style="width:100%;">

                        <tr>
                            <td>※작업내역</td>
                            <?php if(count($etc_tasks) > 0) : ?>
                                <td colspan="3"></td>
                            <?php endif ?>
                        </tr>
                        

                        <?php foreach($etc_tasks as $etc_task) : ?>
                            <tr>
                                <td style="width:200px"><?= $etc_task['created_at'] ?></td>
                                <td style="width:130px"><?= $etc_task['team_name'] ?></td>
                                <td style="width:90px"><?= $etc_task['manday'] ?><span>명</span></td>
                                <td><span class="task_edit" data-task_id="<?= $etc_task['id'] ?>" style="color:blue; cursor:pointer;">[수정]</span></td>
                            </tr>
                        <?php endforeach ?>

                        <tr>
                            <?php if(count($etc_tasks) > 0) : ?>
                                <td colspan="3"></td>
                            <?php endif ?>
                            <td><span id="task_add" style="color:blue; cursor:pointer;">[추가]</span></td>
                        </tr>
                    </table>
            
                </div>


            </div>
        </div>
    </form>

    <!-- 팀변경 modal -->
    <div id="task_change_team_modal" class="ui mini modal">

        <form id="task_change_team_form" class="ui form" method="POST" action="/fm/change_etc_task_team">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label>작업팀</label>
                    <div style="width:278px;">
                        <select class="ui fluid dropdown" name="new_team_id">
                            <option value="">팀이름</option>

                            <?php foreach($teams as $team) : ?>

                            <option value="<?=$team['id']?>" <?= $team['id'] == $this_team['id'] ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                </div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="task_change_team_submit" style="color:#5599DD; cursor:pointer;">변경</span>
                </div>

            </div>

            <input type="hidden" name="task_name" value="<?= $task_name ?>">
            <input type="hidden" name="old_team_id" value="<?= $this_team['id'] ?>">
        </form>
    </div>

    <!-- 작업완료 modal -->
    <div id="task_finish_modal" class="ui mini modal">

        <form id="task_finish_form" class="ui form" method="POST" action="/fm/finish_etc_taskplan">
            <div style="padding:16px;">

                <div style="margin-top:4px; line-height:150%">이 작업을 완료하고 해당 작업계획을 삭제합니다.</div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="task_finish_submit" style="color:#5599DD; cursor:pointer;">확인</span>
                </div>

            </div>

            <input type="hidden" name="task_name" value="<?= $task_name ?>">
            <input type="hidden" name="team_id" value="<?= $this_team['id'] ?>">
        </form>
    </div>

    <!-- 작업삭제 modal -->
    <div id="task_delete_modal" class="ui mini modal">

        <form id="task_delete_form" class="ui form" method="POST" action="/fm/delete_etc_taskplan">
            <div style="padding:16px;">

                <div style="margin-top:4px; line-height:150%">해당 작업의 기록과 작업계획을 모두 삭제하며 복구는 불가합니다.</div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="task_delete_submit" style="color:#5599DD; cursor:pointer;">확인</span>
                </div>

            </div>

            <input type="hidden" name="task_name" value="<?= $task_name ?>">
            <input type="hidden" name="team_id" value="<?= $this_team['id'] ?>">
        </form>
    </div>

    <!-- 작업기록 수정 modal -->
    <div id="task_edit_modal" class="ui mini modal">

        <form id="task_edit_form" class="ui form" method="POST" action="/fm/edit_etc_task">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업일시</label>
                    <div id="task_edit_date" style="width:270px">작업일시</div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:28px">
                    <label>작업팀</label>
                    <div style="width:270px"><?= $this_team['name'] ?></div>
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label>작업인원</label>
                    <input id="input_task_edit_manday" type="number" min="0" name="manday" placeholder="0을 포함하는 자연수" style="width:270px">
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                    <span id="task_delete_submit2" style="color:#5599DD; cursor:pointer;">작업기록 삭제</span>
                    <div class="actions" style="text-align:right;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                        <span id="task_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                    </div>
                </div>

            </div>

            <input type="hidden" name="task_id">
            <input type="hidden" name="team_id" value="<?= $this_team['id'] ?>">
        </form>
    </div>

    <!-- 작업기록추가 modal -->
    <div id="task_add_modal" class="ui mini modal">

        <form id="task_add_form" class="ui form" method="POST" action="/fm/add_etc_task">
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; margin-bottom:8px">
                    <label>작업일시</label>
                    <div id="calendar_display" style="width:270px">1</div>
                </div>

                <div class="ui calendar" id="inline_calendar" style="margin-top:4px"></div>
                <input type="hidden" name="task_calendar" value="0">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <?php
                        $attendance = $this_team['attendance'] != 0 ? $this_team['attendance'] : 1;
                    ?>
                    <label>작업인원</label>
                    <div class="ui input" style="width:250px;">
                        <input type="number" min="1" name="manday" placeholder="0을 포함하지 않는 자연수" value="<?= $attendance ?>">
                    </div>
                </div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="task_add_submit" style="color:#5599DD; cursor:pointer;">추가</span>
                </div>

            </div>

            <input type="hidden" name="task_name" value="<?= $task_name ?>">
            <input type="hidden" name="team_id"  value="<?= $this_team['id'] ?>">
        </form>
    </div>


<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

    <script type="text/javascript">
        
        $('#inline_calendar').calendar({
            type: 'datetime',
            text: {
                months: ['1월,', '2월,', '3월,', '4월,', '5월,', '6월,', '7월,', '8월,', '9월,', '10월,', '11월,', '12월,'],
                monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            },
            ampm: false,
            onChange: function(date) {
                
                year = date.getFullYear().toString();
                month = ('0' + (date.getMonth() + 1)).slice(-2);
                day = ('0' + date.getDate()).slice(-2);
                hour = ('0' + date.getHours()).slice(-2);
                min = ('0' + date.getMinutes()).slice(-2);

                date_str = year + "-" + month + "-" + day + " " + hour +":" + min + ":00";
                
                $('#task_add_form [name=task_calendar]').val(date_str);
                $('#calendar_display').text(date_str);

            }
        });
            
        $(document).ready(function() {
            
            //작업기록팀변경
            $('#task_change_team').click(function() {

                $('#task_change_team_modal').modal('setting', {
                    autofocus: false,
                }).modal('show');

            });
            $('#task_change_team_submit').click(function() {

                $('#task_change_team_form').submit();

            });
        
            //작업완료
            $('#task_finish').click(function() {

                $('#task_finish_modal').modal('show');

            });
            $('#task_finish_submit').click(function() {

                $('#task_finish_form').submit();

            });
            
            //작업삭제
            $('#task_delete').click(function() {

                $('#task_delete_modal').modal('show');

            });
            $('#task_delete_submit').click(function() {

                $('#task_delete_form').submit();

            });
            
            //작업기록수정, 삭제
            $('.task_edit').click(function() {

                var date = $(this).parent().prev().prev().prev().text();
                var manday = $(this).parent().prev().clone().children().remove().end().text();
                var task_id = $(this).data('task_id');

                $('#task_edit_date').text(date);
                $('#input_task_edit_manday').val(manday);
                $('#task_edit_form [name=task_id]').val(task_id);
                
                $('#task_edit_modal').modal('show');

            });
            $('#task_delete_submit2').click(function() {

                $('#task_edit_form [name=manday]').val(-1);
                $('#task_edit_form').submit();

            });
            $('#task_edit_submit').click(function() {

                $('#task_edit_form').submit();

            });

            //작업기록추가
            $('#task_add').click(function() {

                var now = new Date();
                now = now.getFullYear() + '-' + ('0'+(now.getMonth()+1)).slice(-2) + '-' + ('0'+now.getDate()).slice(-2) + ' ' + ('0'+now.getHours()).slice(-2) + ':' + ('0'+now.getMinutes()).slice(-2) + ":" + ('0'+now.getSeconds()).slice(-2);

                $('#inline_calendar').calendar('set date', now);
                $('#inline_calendar').calendar('set mode', 'day');
                $('#task_add_modal').modal('show');

            });
            $('#task_add_submit').click(function() {

                $('#task_add_form').submit();

            });

        });

    </script>

<?= $this->endSection() ?>