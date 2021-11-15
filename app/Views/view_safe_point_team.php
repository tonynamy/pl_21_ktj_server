<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px">

                <div style="padding:16px;">
                    <i class="arrow left icon" onclick="location.href='/fm/view_safe_point/<?=$target_time->getTimestamp()?>'" style="cursor: pointer;"></i> <!-- 뒤로가기 -->
                    <select id="team_select" class="ui dropdown" name="team">
                        <option value="">팀이름</option>

                        <?php foreach($teams as $team) : ?>

                        <option value="<?=$team['id']?>" <?= $team['id'] == $this_team['id'] ? "SELECTED" : "" ?> > <?= $team['name'] ?> </option>

                        <?php endforeach ?>

                    </select>

                    <?php if($this_team['id'] != 0) : ?>
                    
                        <div style="display:flex; justify-content:space-between; margin-top:32px; margin-bottom:48px">
                            <a href="<?= route_to('view_safe_point_team', $this_team['id'], $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                            <span style="font-size:x-large;"><?= $target_time->getMonth() ?>월</span>
                            <?php if(!$is_after) : ?>
                                <a href="<?= route_to('view_safe_point_team', $this_team['id'], $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                            <?php endif ?>
                        </div>

                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:large;"><?= $this_team['name'] ?> 안전점수</span>

                            <?php if(!$is_before) : ?>
                                <button class="filletbutton add_point" type="button" style="margin-right:8px">점수 주기</button>
                            <?php endif ?>
                        </div>

                        <div style="width:100%; margin-bottom:16px">
                            <table class="ui compact selectable celled table" style="table-layout:fixed;">
                                <thead class="full-width">
                                    <tr align="center">
                                        <th style="font-weight:normal;">일시</th>
                                        <th style="font-weight:normal;">내용</th>
                                        <th style="font-weight:normal;">점수</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    
                                    <?php $sum = 0 ?>
                                    <?php foreach($team_safe_points as $team_safe_point) : ?>

                                        <tr align="center" class="edit_point" data-tspid="<?=$team_safe_point['id']?>">
                                            <td><?= $team_safe_point['created_at'] ?></td>
                                            <td><?= $team_safe_point['name'] ?></td>
                                            <td><?= $team_safe_point['point'] == 0? "무효" : $team_safe_point['point'] ?></td>
                                        </tr>
                                        <?php $sum += $team_safe_point['point']?>
                                    <?php endforeach ?>

                                </tbody>
                            </table>
                    
                        </div>

                        <div align="right" style="margin-bottom:16px;">
                            <span style="margin-right:8px">합계</span>
                            <span style="margin-right:8px; font-size:large"><?= 100 + $sum ?>점</span>
                        </div>

                    <?php endif ?>
            
                </div>

            </div>
        </div>
        
    </form>

    <!-- 점수주기 modal -->
    <div id="add_safe_point_modal" class="ui mini modal">

        <div style="padding-top:4px; padding-bottom:4px">

            <?php foreach($safe_points as $safe_point) : ?>

                <div onclick="javascript:location.href='<?=route_to('add_team_safe_point', $this_team['id'], $safe_point['id'])?>'" style="display:flex; justify-content:space-between; padding:16px; cursor:pointer;"><span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span></div>

            <?php endforeach ?>

            <div class="actions" style="text-align:right; padding:16px;">
                <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
            </div>
            
        </div>

    </div>

    <!-- 점수수정 modal -->
    <div id="edit_safe_point_modal" class="ui mini modal">

        <div style="padding-top:4px; padding-bottom:4px">

            <?php foreach($safe_points as $safe_point) : ?>

                <div class="item safe_point" data-spid="<?=$safe_point['id']?>" style="display:flex; justify-content:space-between; padding:16px;  cursor:pointer;"><span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span></div>

            <?php endforeach ?>

            <div class="item safe_point" data-spid="0" style="padding:16px; cursor:pointer;">점수 무효</div>

            <div class="actions" style="display:flex; justify-content:space-between; padding:16px;">
                <span class="item safe_point"data-spid="-1"  style="color:#5599DD; cursor:pointer;">삭제</span>
                <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
            </div>

        </div>

        <form id="edit_safe_point_form" class="ui form" method="POST" action="/fm/edit_team_safe_point">
            <input id="team_safe_point_id" type="hidden" name="team_safe_point_id" />
            <input id="new_safe_point_id" type="hidden" name="new_safe_point_id" />
        </form>

    </div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_safe_point_team/' + this.value;

        });

        $('button.add_point').click(function() {

            $('#add_safe_point_modal').modal('show');

        });
        
        $('tr.edit_point').click(function() {

            $('#team_safe_point_id').val($(this).data('tspid'));

            $('#edit_safe_point_modal').modal('show');

        });

        $('.item.safe_point').click(function() {

            $('#new_safe_point_id').val($(this).data('spid'));

            $('#edit_safe_point_form').submit();

        });

    });

</script>

<?= $this->endSection() ?>