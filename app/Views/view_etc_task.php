<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:1000px;">

                <div style="display:flex; justify-content:space-between; align-items:center">

                    <div style="display:flex; align-items:center; padding:16px">
                        <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                        <select id="mode_select" class="ui dropdown">
                            <option value="1"> 도면있는 작업 조회 </option>
                            <option value="2" selected> 도면없는 작업 조회 </option>
                        </select>
                    </div>

                    <button class="filletbutton" type="button" style="margin-right:16px" onclick="location.href='/fm/download_etc_task'">엑셀로 저장</button>

                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <div style="width:100%; padding:16px;">

                    <div style="text-align:right; margin-bottom:16px">
                        <button id="taskplan_add" class="filletbutton" type="button">작업계획추가</button>
                    </div>

                    <table class="ui sortable compact selectable celled table" style="table-layout:fixed;">
                        <thead>
                            <tr align="center">
                                <th height="40px" style="font-weight:normal; border-right:0px; padding:0px">작업명</th>
                                <th width="150px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">작업팀</th>
                                <th width="150px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">총투입인원</th>
                                <th width="150px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">작업시작일</th>
                                <th width="150px" style="font-weight:normal; border-left:0px; border-right:0px; padding:0px">작업완료일</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach($etc_tasks as $task_name => $etc_task_teams) : ?>

                                <?php foreach($etc_task_teams as $team_info => $etc_task) : ?>

                                    <?php //var_dump($etc_task); exit; ?>

                                    <tr align="center" style="cursor:pointer;" onclick='javascript:location.href="<?=route_to('view_etc_task_info', urlencode(explode('__', $team_info)[0]), urlencode($task_name))?>"'>
                                        
                                        <td><?=$task_name?></td>
                                        <td><?=explode('__', $team_info)[1]?></td>
                                        <td><?=$etc_task['total_manday']?>명(<?=$etc_task['total_task']?>회)</td>
                                        <td><?=$etc_task['started_at'] != null ? $etc_task['started_at']->toDateString() : ""?></td>
                                        <td><?=$etc_task['finished_at'] != null ? $etc_task['finished_at']->toDateString() : ""?></td>
                                    </tr>
                                    
                                <?php endforeach ?>

                            <?php endforeach ?>

                        </tbody>
                    </table>
            
                </div>


            </div>
        </div>
        
    </form>

    <!-- 작업계획추가 modal -->
    <div id="taskplan_add_modal" class="ui mini modal">

        <form id="taskplan_add_form" class="ui form" method="POST" action="/fm/add_etc_taskplan">
            
            <div style="padding:16px;">

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                    <label>작업명</label>
                    <input type="text" name="task_name" placeholder="도면이 없는 작업만 등록" value="" style="width:270px;">
                </div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                    <label>작업팀</label>
                    <div style="width:270px;">
                        <select id="team_select" class="ui fluid dropdown" name="team_id">
                            
                            <?php foreach($teams as $team) : ?>

                                <option value="<?=$team['id']?>" > <?= $team['name'] ?> </option>

                            <?php endforeach ?>

                        </select>
                    </div>
                </div>
                <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="taskplan_add_submit" style="color:#5599DD; cursor:pointer;">확인</span>
                </div>
            </div>

        </form>

    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        $('#mode_select').val(2);
        $('#mode_select').on('change', function() {
            if(this.value == 1) {
            location.href = '/fm/view_facility';
            }
        });

        $('#taskplan_add').click(function() {

            $('#taskplan_add_modal').modal('show');

        });

        $('#taskplan_add_submit').click(function() {

            $('#taskplan_add_form').submit();

        });

        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>