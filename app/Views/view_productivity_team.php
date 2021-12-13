<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px">

                <div style="padding:16px;">
                    <i class="arrow left icon" onclick="location.href='/fm/view_productivity/<?= $target_time->getTimestamp() ?>'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <select id="team_select" class="ui dropdown" name="team">
                        <option value="">팀이름</option>

                        <?php foreach($teams as $team) : ?>

                        <option value="<?=$team['id']?>" <?= $team['id'] == $this_team['id'] ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                        <?php endforeach ?>

                    </select>

                    <?php if($this_team['id'] != 0) : ?>
                    
                        <div style="display:flex; justify-content:space-between; margin-top:32px; margin-bottom:48px">
                            <div style="flex:1">
                                <a href="<?= route_to('view_productivity_team', $this_team['id'], $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                            </div>
                            <div style="flex:1; text-align:center;">
                                <span style="font-size:x-large;"><?= $target_time->getMonth() ?>월</span>
                            </div>
                            <div style="flex:1; text-align:right;">
                                <?php if(!$is_after) : ?>
                                    <a href="<?= route_to('view_productivity_team', $this_team['id'], $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                                <?php endif ?>
                            </div>
                        </div>

                        <div style="margin-bottom:8px; font-size:large;"><?= $this_team['name'] ?> 수평비계</div>

                        <div style="width:100%; margin-bottom:16px">
                            <table class="ui sortable compact selectable celled table">
                                <thead class="full-width">
                                    <tr align="center">
                                        <th style="font-weight:normal;">작업</th>
                                        <th width="150px" style="font-weight:normal;">물량</th>
                                        <th width="150px" style="font-weight:normal;">인원</th>
                                        <th width="150px" style="font-weight:normal;">1인당 생산성</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    
                                    <?php $sum = 0 ?>
                                    <?php foreach($tasks as $task) : ?>

                                        <?php if($task['is_square_current'] == 1) continue ?>

                                        <tr align="center" style="cursor:pointer;" onclick='javascript:location.href="<?=route_to('view_facility_max_rnum', urlencode($task['facility_serial']))?>"'>
                                            <td><?= $task['facility_serial'] ?></td>
                                            <td><?= $task['size_current'] ?> ㎥</td>
                                            <td><?= $task['manday_max'] ?></td>
                                            <td><?= round($task['size_current'] / $task['manday_max'], 1) ?>㎥</td>
                                        </tr>
                                        <?php $sum += $task['size_current'] / $task['manday_max'] ?>
                                    <?php endforeach ?>

                                </tbody>
                            </table>
                    
                        </div>

                        <div align="right" style="margin-bottom:16px;">
                            <span style="margin-right:8px">합계</span>
                            <span style="margin-right:8px; font-size:large"><?= round($sum, 1) ?>㎥</span>
                        </div>

                        <div style="height:1px; background-color:#e8e9e9; margin-bottom:16px;"></div>

                        <div style="margin-bottom:8px; font-size:large;"><?= $this_team['name'] ?> 달대비계</div>

                        <div style="width:100%; margin-bottom:16px">
                            <table class="ui sortable compact selectable celled table">
                                <thead class="full-width">
                                    <tr align="center">
                                        <th style="font-weight:normal;">작업</th>
                                        <th width="150px" style="font-weight:normal;">물량</th>
                                        <th width="150px" style="font-weight:normal;">인원</th>
                                        <th width="150px" style="font-weight:normal;">1인당 생산성</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php $sum = 0 ?>
                                    <?php foreach($tasks as $task) : ?>

                                        <?php if($task['is_square_current'] == 0) continue ?>

                                        <tr align="center" style="cursor:pointer;" onclick='javascript:location.href="<?=route_to('view_facility_max_rnum', urlencode($task['facility_serial']))?>"'>
                                            <td><?= $task['facility_serial'] ?></td>
                                            <td><?= $task['size_current'] ?> ㎡</td>
                                            <td><?= $task['manday_max'] ?></td>
                                            <td><?= round($task['size_current'] / $task['manday_max'] , 1) ?>㎡</td>
                                        </tr>
                                        <?php $sum += $task['size_current'] / $task['manday_max'] ?>
                                    <?php endforeach ?>

                                </tbody>
                            </table>
                    
                        </div>
                        
                        <div align="right" style="margin-bottom:16px;">
                            <span style="margin-right:8px">합계</span>
                            <span style="margin-right:8px; font-size:large"><?= round($sum, 1) ?>㎡</span>
                        </div>


                        <div style="height:1px; background-color:#e8e9e9; margin-bottom:24px;"></div>

                        <div style="margin-bottom:8px; font-size:large;"><?= $this_team['name'] ?> 맨데이</div>

                        <div style="width:100%; margin-bottom:16px">
                            <table class="ui sortable compact selectable celled table">
                                <thead class="full-width">
                                    <tr align="center">
                                        <th width="150px" style="font-weight:normal;">날짜</th>
                                        <th style="font-weight:normal;">작업</th>
                                        <th width="150px" style="font-weight:normal;">내용</th>
                                        <th width="150px" style="font-weight:normal;">인원</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    
                                    <?php $sum = 0 ?>
                                    <?php foreach($tasks_manday as $task) : ?>
                                        <tr align="center" style="cursor:pointer;" onclick='javascript:location.href="<?=route_to('view_manday_team', $task['team_id'], CodeIgniter\I18n\Time::createFromDate(explode('-', $task['s_created_at'])[0], explode('-', $task['s_created_at'])[1], explode('-', $task['s_created_at'])[2])->getTimestamp())?>"'>
                                            <td><?= $task['s_created_at'] ?></td>
                                            <td><?= $task['facility_serial'] ?></td>
                                            <td><?= getTaskTypeText($task['type']) ?>작업</td>
                                            <td><?= $task['manday_max'] ?></td>
                                        </tr>

                                        <?php $sum += $task['manday_max'] ?>
                                    <?php endforeach ?>

                                </tbody>
                            </table>
                    
                        </div>
                        
                        <div align="right" style="margin-bottom:8px;">
                            <span style="margin-right:8px">합계</span>
                            <span style="margin-right:8px; font-size:large"><?= $sum ?>공수</span>
                        </div>

                    <?php endif ?>
            
                </div>

            </div>
        </div>
        
    </form>

    <div id="edit_manday_modal" class="ui mini modal">

        <div style="padding-top:4px; padding-bottom:4px">

            <div style="display:flex; align-items:center; padding:16px">
                <div style="width:70px">작업명</div>
                <div>SAM-001</div>
            </div>
            <div style="display:flex; align-items:center; padding:16px">
                <div style="width:70px">내용</div>
                <div>해체작업</div>
            </div>
            <div class="ui input" style="display:flex; align-items:center; padding:6px 16px 6px 16px">
                <div style="width:70px">인원</div>
                <input type="text" name="manday" placeholder="0을 포함한 자연수" value="">
            </div>
            <div class="actions" style="text-align:right; padding:16px">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span style="color:#5599DD; cursor:pointer;">수정</span>
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
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_productivity_team/' + this.value + '/<?= $target_time->getTimestamp() ?>';

        });
        
        $('tr.edit_manday').click(function() {

            $('#edit_manday_modal').modal('show');

        });

        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>