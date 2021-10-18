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
                        <a href="<?= route_to('view_safe_point_team', $this_team['id'], $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                    </div>

                    <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                        <span style="font-size:large;"><?= $this_team['name'] ?> 안전점수</span>
                        <button class="filletbutton add_point" type="button" style="margin-right:8px">점수 주기</button>
                    </div>

                    <div style="width:100%; margin-bottom:16px">
                        <table class="ui compact selectable celled table" style="table-layout:fixed;">
                            <thead class="full-width">
                                <tr align="center">
                                    <th style="font-weight:normal;">일시</th>
                                    <th style="font-weight:normal;">항목이름</th>
                                    <th style="font-weight:normal;">점수</th>
                                </tr>
                            </thead>

                            <tbody>
                                
                                <?php $sum = 0 ?>
                                <?php foreach($team_safe_points as $team_safe_point) : ?>

                                    <tr align="center" class="edit_point" data-id="<?=$team_safe_point['id']?>">
                                        <td><?= $team_safe_point['created_at'] ?></td>
                                        <td><?= $team_safe_point['name'] ?></td>
                                        <td><?= $team_safe_point['point'] ?></td>
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

<div id="add_point" class="ui mini modal">
    <div style="padding:8px;">

        <?php foreach($safe_points as $safe_point) : ?>

            <div onclick="javascript:location.href='<?=route_to('add_team_safe_point', $this_team['id'], $safe_point['id'])?>'" style="display:flex; justify-content:space-between; padding:16px; cursor:pointer;"><span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span></div>

        <?php endforeach ?>
        
    </div>

</div>

<div id="edit_point" class="ui mini modal">

    <?php foreach($safe_points as $safe_point) : ?>

        <div class="edit_point_element" data-id="<?=$safe_point['id']?>" style="display:flex; justify-content:space-between; padding:16px;  cursor:pointer;"><span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span></div>

    <?php endforeach ?>

    <div class="edit_point_element" data-id="-1" style="padding:16px; cursor:pointer;">점수 삭제</div>

    <form id="edit_point_form" class="ui form" method="POST" action="/fm/edit_team_safe_point">
        <input id="edit_point_sp_id" type="hidden" name="team_safe_point_id" />
        <input id="edit_point_point_id" type="hidden" name="point_id" />
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

            $('#add_point').modal('show');

        });
        
        $('tr.edit_point').click(function() {

            $('#edit_point_sp_id').val($(this).data('id'));

            $('#edit_point').modal('show');

        });

        $('.edit_point_element').click(function() {

            $('#edit_point_point_id').val($(this).data('id'));

            $('#edit_point_form').submit();

        });

    });

</script>

<?= $this->endSection() ?>