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
                    <a href="<?= route_to('view_safe_point', $this_team, $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                    <span style="font-size:x-large;"><?=$target_time->getMonth()?>월</span>
                    <a href="<?= route_to('view_safe_point', $this_team, $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                </div>

                <div style="margin-bottom:8px; font-size:large;">안전점수</div>

                <div style="width:100%; margin-bottom:16px">
                    <table class="ui sortable compact selectable celled table">
                        <thead class="full-width">
                            <tr align="center">
                                <th width="150px" style="font-weight:normal;">항목이름</th>
                                <th width="150px" style="font-weight:normal;">점수</th>
                            </tr>
                        </thead>

                        <tbody>

                        <?php $sum = 0 ?>
                            <?php foreach($safe_points as $safe_point) : ?>

                                <tr align="center">
                                    <td><?=$safe_point['name']?></td>
                                    <td><?=$safe_point['point']?></td>
                                </tr>                                

                                <?php $sum += $safe_point['point'] ?>

                            <?php endforeach ?>


                        </tbody>
                    </table>

                    <div align="right" style="margin-bottom:16px;">
                        <span style="margin-right:8px">합계</span>
                        <span style="margin-right:8px; font-size:large"><?=100+$sum?></span>
                    </div>
            
                </div>

            </div>
        </div>
        
    </form>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_safe_point/' + this.value;

        });

    });

</script>

<?= $this->endSection() ?>