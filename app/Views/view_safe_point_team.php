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
                            <div style="flex:1;">
                                <?php if(!$is_first_month) : ?>
                                    <a href="<?= route_to('view_safe_point_team', $this_team['id'], $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                                <?php endif ?>
                            </div>
                            <div style="flex:1; text-align:center;">
                                <span style="font-size:x-large;"><?= $target_time->getMonth() ?>월</span>
                            </div>
                            <div style="flex:1; text-align:right;">
                                <?php if(!$is_after) : ?>
                                    <a href="<?= route_to('view_safe_point_team', $this_team['id'], $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                                <?php endif ?>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                            <span style="font-size:large;"><?= $this_team['name'] ?> 안전점수</span>
                            <button id="add_safe_point" class="filletbutton" type="button">점수 주기</button>
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

                                        <tr align="center" class="edit_safe_point" data-tspid="<?=$team_safe_point['id']?>">
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
    <div id="add_safe_point_modal" class="ui small modal">

        <form id="add_safe_point_form"  method="POST" action="/fm/add_team_safe_point">
                
            <div style="display:flex; padding-top:4px; padding-bottom:4px">
                <div style="flex:1; padding:16px;">

                    <div style="display:flex; justify-content:space-between; margin-bottom:16px">
                        <label>점수일시</label>
                        <div id="calendar_display" style="width:290px"></div>
                    </div>

                    <div id="inline_calendar" class="ui calendar"></div>
                    <input type="hidden" name="team_sp_date">
                    
                </div>

                <div style="width:1px; background-color:#e8e9e9;"></div>

                <div style="flex:1;">

                    <?php foreach($safe_points as $safe_point) : ?>

                        <div class="add_safe_point_item" data-spid="<?=$safe_point['id']?>"  style="display:flex; justify-content:space-between; padding:16px; cursor:pointer;">
                            <span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span>
                        </div>

                    <?php endforeach ?>

                    <div class="actions" style="text-align:right; padding:16px;">
                        <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>
                    </div>

                </div>
            </div>
                
            <input type="hidden" name="team_id" value="<?= $this_team['id'] ?>">
            <input type="hidden" name="sp_id">
        </form>

    </div>

    <!-- 점수수정 modal -->
    <div id="edit_safe_point_modal" class="ui mini modal">

        <form id="edit_safe_point_form" method="POST" action="/fm/edit_team_safe_point">
            <div style="padding-top:4px; padding-bottom:4px">

                <?php foreach($safe_points as $safe_point) : ?>

                    <div class="edit_safe_point_item" data-spid="<?=$safe_point['id']?>" style="display:flex; justify-content:space-between; padding:16px;  cursor:pointer;">
                        <span><?=$safe_point['name']?></span><span><?=$safe_point['point']?></span>
                    </div>

                <?php endforeach ?>

                <div class="edit_safe_point_item" data-spid="0" style="padding:16px; cursor:pointer;">점수 무효</div>

                <div class="actions" style="display:flex; justify-content:space-between; padding:16px;">

                    <span class="edit_safe_point_item" data-spid="-1"  style="color:#5599DD; cursor:pointer;">점수 삭제</span>
                    <span class="cancel" style="color:#5599DD; cursor:pointer;">취소</span>

                </div>
            </div>

            <input type="hidden" name="team_sp_id">
            <input type="hidden" name="sp_id">
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
            
            $('#add_safe_point_form [name=team_sp_date]').val(date_str);
            $('#calendar_display').text(date_str);

        }
    });

    $(document).ready(function() {
        
        $('#team_select').on('change', function() {

            location.href = '/fm/view_safe_point_team/' + this.value + '/<?= $target_time->getTimestamp() ?>';

        });

        //안전점수 추가
        $('#add_safe_point').click(function() {
            
            var now = new Date();
            now = now.getFullYear() + '-' + ('0'+(now.getMonth()+1)).slice(-2) + '-' + ('0'+now.getDate()).slice(-2) + ' ' + ('0'+now.getHours()).slice(-2) + ':' + ('0'+now.getMinutes()).slice(-2) + ":" + ('0'+now.getSeconds()).slice(-2);

            $('#calendar_display').text(now);
            $('#inline_calendar').calendar('set date', now);
            $('#inline_calendar').calendar('set mode', 'day');
            $('#add_safe_point_form [name=team_sp_date]').val(now);
            $('#add_safe_point_modal').modal('show');

        });
        $('.add_safe_point_item').click(function() {
            
            $('#add_safe_point_form [name=sp_id]').val($(this).data('spid'));
            $('#add_safe_point_form').submit();

        });

        //안전점수 수정 
        $('.edit_safe_point').click(function() {

            $('#edit_safe_point_form [name=team_sp_id]').val($(this).data('tspid'));

            $('#edit_safe_point_modal').modal('show');

        });
        $('.edit_safe_point_item').click(function() {

            $('#edit_safe_point_form [name=sp_id]').val($(this).data('spid'));
            $('#edit_safe_point_form').submit();

        });

    });

</script>

<?= $this->endSection() ?>