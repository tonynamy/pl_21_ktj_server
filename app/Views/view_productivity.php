<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:800px">

                <div style="padding:16px;">
                    <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                    <label>생산성 조회</label>
                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <div style="padding:16px;">
                        
                    <div style="display:flex; justify-content:space-between; margin-top:18px; margin-bottom:48px">
                        <a href="<?= route_to('view_productivity', $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                        <span style="font-size:x-large;"><?= $target_time->getMonth() ?>월</span>
                        <a href="<?= route_to('view_productivity', $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                    </div>

                    <div style="width:100%; margin-bottom:16px">
                        <table class="ui sortable selectable celled table">
                            <thead class="full-width">
                                <tr align="center">
                                    <th width="25%" style="font-weight:normal;">팀</th>
                                    <th width="25%" style="font-weight:normal;">1인당 수평비계 생산성</th>
                                    <th width="25%" style="font-weight:normal;">1인당 달대비계 생산성</th>
                                    <th width="25%" style="font-weight:normal;">맨데이 합계</th>
                                </tr>
                            </thead>

                            <tbody>
                                
                                <?php foreach($teams as $team) : ?>

                                    <tr class="productivity select" data-id="<?= $team['id'] ?>" align="center">
                                        <td><?= $team['name'] ?></td>
                                        <td><?= $totals_cube[$team['id']] ?>㎥</td>
                                        <td><?= $totals_square[$team['id']] ?>㎡</td>
                                        <td><?= $totals_manday[$team['id']] ?>공수</td>
                                    </tr>
                                <?php endforeach ?>

                            </tbody>
                        </table>
                
                    </div>
                </div>

            </div>
        </div>
        
    </form>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {
        
        $('tr.productivity.select').click(function() {

            var id = $(this).data('id');

            location.href = '/fm/view_productivity_team/' + id + '/<?= $target_time->getTimestamp() ?>';

        });

        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>