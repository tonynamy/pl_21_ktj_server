<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px; padding:16px;">

                <div style="display:flex; align-items:center; margin-top:8px">
                    <i class="arrow left icon" onclick="location.href='/fm/view_productivity/'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <span style="font-size:x-large; font-weight:normal;"><?=$team['name']?>&nbsp;<?=$target_time->toDateString()?>&nbsp;맨데이작업</span>
                </div>

                <div style="width:100%; margin-top:16px;">
                    ※가장 인원이 큰 작업이 생산성 조회에서 조회됩니다.<br>
                    ※삭제후에는 복구가 불가하기 때문에 인원을 0으로 수정하는 것을 권장합니다.
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

                                <tr class="edit_manday" align="center">
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

    <div id="edit_manday_modal" class="ui mini modal">

        <div style="padding:16px">

            <div style="display:flex; align-items:center; margin-top:4px">
                <div style="width:70px">날짜</div>
                <div>13:31:42</div>
            </div>
            <div style="display:flex; align-items:center; margin-top:24px">
                <div style="width:70px">작업명</div>
                <div>굴다리에서 싱하형과 양중작업</div>
            </div>
            <div style="display:flex; align-items:center; margin-top:24px">
                <div style="width:70px">내용</div>
                <div>기타작업</div>
            </div>
            <div class="ui input" style="display:flex; align-items:center; margin-top:12px">
                <div style="width:70px">인원</div>
                <input type="text" name="manday" placeholder="0을 포함한 자연수" value="7">
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                <span style="color:#5599DD; cursor:pointer;">삭제(복구불가)</span>
                <div class="actions" style="text-align:right;">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span style="color:#5599DD; cursor:pointer;">수정</span>
                </div>
            </div>

        </div>


        <form id="edit_point_form" class="ui form" method="POST" action="/fm/edit_team_safe_point">
            <input id="edit_task_id" type="hidden" name="task_id" />
            <input id="edit_task_manday" type="hidden" name="task_manday" />
        </form>

    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {
        
        $('tr.edit_manday').click(function() {

            $('#edit_manday_modal').modal('show');

        });

        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>