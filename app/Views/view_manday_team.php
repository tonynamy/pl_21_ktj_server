<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px; padding:16px;">

                <div style="display:flex; align-items:center; margin-top:5px">
                    <i class="arrow left icon" onclick="location.href='/fm/view_productivity_team/<?= $this_team['id'] ?>/<?= $target_time->getTimestamp() ?>'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <span style="font-size:x-large; font-weight:normal;"><?= $this_team['name'] ?> <?= $target_time->toDateString() ?> 맨데이작업</span>
                </div>

                <div style="width:100%; margin-top:16px;">
                    ※가장 인원이 큰 작업기록이 생산성 조회에서 조회됩니다.<br>
                    ※작업내역을 추가하거나 삭제하려면 해당 작업으로 이동해야합니다.
                </div>

                <div style="width:100%; margin-top:8px;">
                    <table class="ui sortable compact selectable celled table">
                        <thead class="full-width">
                            <tr align="center">
                                <th width="150px" style="font-weight:normal;">시간</th>
                                <th style="font-weight:normal;">작업명</th>
                                <th width="150px" style="font-weight:normal;">내용</th>
                                <th width="150px" style="font-weight:normal;">투입인원</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($tasks as $task) : ?>

                                <?php $task_time = CodeIgniter\I18n\Time::createFromFormat('Y-m-d H:i:s', $task['created_at']); ?>
                                    <tr class="manday_task" data-task_id="<?= $task['id'] ?>" data-task_type="<?= $task['type'] ?>" data-task_name="<?= $task['facility_serial'] ?>" data-task_manday="<?= $task['manday'] ?>" data-task_time="<?= $task_time ?>" align="center">
                                        <td><?=explode(' ', $task_time->toDateTimeString())[1]?></td>
                                        <td><?=$task['facility_serial']?></td>
                                        <td><?=getTaskTypeText($task['type'])?>작업</td>
                                        <td><?=$task['manday']?>명</td>
                                    </tr>

                            <?php endforeach ?>
                            
                        </tbody>
                    </table>
            
                </div>


            </div>
        </div>
        
    </form>

    <div id="edit_manday_task_modal" class="ui mini modal">

        <form id="edit_manday_task_form" class="ui form" method="POST" action="/fm/edit_etc_task_manday">

            <div style="padding:16px">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업일시</label>
                    <div id="edit_task_time" style="width:270px">작업일시</div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px">
                    <label>작업명</label>
                    <div id="edit_task_name" style="width:270px">작업명</div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px">
                    <label>내용</label>
                    <div style="width:270px">기타작업</div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px">
                    <label>작업인원</label>
                    <input type="number" min="0" name="manday" placeholder="0을 포함한 자연수" style="width:270px">
                </div>

                <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                    <span id="move_to_task" style="color:#5599DD; cursor:pointer;">해당작업으로 이동</span>
                    <div class="actions" style="text-align:right;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                        <span id="edit_manday_task_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                    </div>
                </div>

            </div>

            <input type="hidden" name="task_id">
            <input type="hidden" name="task_type">
            <input type="hidden" name="team_id" value="<?= $this_team['id'] ?>">
            <input type="hidden" name="target_time" value="<?= $target_time->getTimestamp() ?>">

        </form>
    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {

        $('.manday_task').click(function() {

            var task_id = $(this).data('task_id');
            var task_name = $(this).data('task_name');
            var task_type = $(this).data('task_type');
            var task_time = $(this).data('task_time');
            var task_manday = $(this).data('task_manday');

            $('#edit_task_time').text(task_time);
            $('#edit_task_name').text(task_name);
            $('#edit_manday_task_form [name=manday]').val(task_manday);
            $('#edit_manday_task_form [name=task_id]').val(task_id);
            $('#edit_manday_task_form [name=task_type]').val(task_type);

            $('#edit_manday_task_modal').modal('show');

        });
        $('#move_to_task').click(function() {

            var task_type = $('#edit_manday_task_form [name=task_type]').val();

            //일반작업일떄
            if(task_type != 4) {
                //location.href='/fm/view_facility_max_rnum/' + task_name;
                window.open('/fm/view_facility_max_rnum/' + $('#edit_task_name').text());

            //기타작업일떄
            } else {
                window.open('/fm/view_etc_task_info/<?= $this_team['id'] ?>/' + $('#edit_task_name').text());
                
            }

            //$('#edit_manday_task_modal').modal('hide');
            window.location.reload();

        });
        $('#edit_manday_task_submit').click(function() {

            $('#edit_manday_task_form').submit();

        });
        
        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>