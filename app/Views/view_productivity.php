<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px">

                <div style="width:330px; display:flex; align-items:center;">
                    <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                    <label style="width:80px">팀선택</label>
                    <select id="team_select" class="ui dropdown" name="team">
                        <option value="">팀이름</option>

                        <?php foreach($teams as $team) : ?>

                        <option value="<?=$team['id']?>" <?= $team['id'] == $this_team ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                        <?php endforeach ?>

                    </select>
                </div>

                
                <div style="display:flex; justify-content:space-between; margin-top:48px; margin-bottom:48px">
                    <a href="<?= route_to('view_productivity', $this_team, $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                    <span style="font-size:x-large;"><?=$target_time->getMonth()?>월</span>
                    <a href="<?= route_to('view_productivity', $this_team, $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                </div>

                <div style="margin-bottom:8px; font-size:large;">물량(루베)</div>

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

                                <?php if($task['type'] != 1 || $task['is_square_current'] == 1) continue ?> 

                                <tr align="center">
                                    <td><?=$task['facility_serial']?></td>
                                    <td><?=$task['size_current']?>㎥</td>
                                    <td><?=$task['manday_max']?></td>
                                    <td><?=$task['size_current']/$task['manday_max']?>㎥</td>
                                </tr>

                                <?php $sum += $task['size_current']/$task['manday_max'] ?>

                            <?php endforeach ?>


                        </tbody>
                    </table>
            
                </div>

                <div align="right" style="margin-bottom:16px;">
                    <span style="margin-right:8px">합계</span>
                    <span style="margin-right:8px; font-size:large"><?=$sum?>㎥</span>
                </div>

                <div style="height:1px; background-color:#e8e9e9; margin-bottom:16px;"></div>

                <div style="margin-bottom:8px; font-size:large;">물량(헤베)</div>

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

                                <?php if($task['type'] != 1 || $task['is_square_current'] == 0) continue ?> 

                                <tr align="center">
                                    <td><?=$task['facility_serial']?></td>
                                    <td><?=$task['size_current']?>㎥</td>
                                    <td><?=$task['manday_max']?></td>
                                    <td><?=$task['size_current']/$task['manday_max']?>㎥</td>
                                </tr>

                                <?php $sum += $task['size_current']/$task['manday_max'] ?>

                            <?php endforeach ?>

                        </tbody>
                    </table>
            
                </div>
                
                <div align="right" style="margin-bottom:16px;">
                    <span style="margin-right:8px">합계</span>
                    <span style="margin-right:8px; font-size:large"><?=$sum?>㎥</span>
                </div>


                <div style="height:1px; background-color:#e8e9e9; margin-bottom:24px;"></div>

                <div style="margin-bottom:8px; font-size:large;">그외작업(맨데이)</div>

                <div style="width:100%; margin-bottom:16px">
                    <table class="ui sortable compact selectable celled table">
                        <thead class="full-width">
                            <tr align="center">
                                <th style="font-weight:normal;">작업</th>
                                <th width="150px" style="font-weight:normal;">날짜</th>
                                <th width="150px" style="font-weight:normal;">인원</th>
                                <th width="150px" style="font-weight:normal;">내용</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $sum = 0 ?>
                            <?php foreach($tasks_manday as $task) : ?>

                                <tr align="center">
                                    <td><?=$task['facility_serial']?></td>
                                    <td><?=$task['s_created_at']?></td>
                                    <td><?=$task['manday_max']?></td>
                                    <td><?=getTaskTypeText($task['type']).'작업'?></td>
                                </tr>

                                <?php $sum += $task['manday_max'] ?>

                            <?php endforeach ?>

                        </tbody>
                    </table>
            
                </div>
                
                <div align="right" style="margin-bottom:8px;">
                    <span style="margin-right:8px">합계</span>
                    <span style="margin-right:8px; font-size:large"><?=$sum?>공수</span>
                </div>


            </div>
        </div>
        
    </form>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_productivity/' + this.value;

        });

    });

</script>

<?= $this->endSection() ?>