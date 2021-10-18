<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

 <form method="POST">

     <div style="width:fit-content; margin:0 auto; padding:16px;">
         <div class="uiframe" style="width:800px">

             <div style="padding:16px;">
                 <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                 <label>안전점수 조회</label>
             </div>

             <div style="height:1px; background-color:#e8e9e9;"></div>

             <div style="width:100%; display:flex; justify-content:space-between; padding:16px;">

                 <div style="width:270px; margin-top:16px; margin-bottom:16px;">
                     <table width="100%" style="table-layout:fixed;">
                         <colgroup>
                             <col width="60%">
                             <col width="20%">
                             <col width="20%">
                         </colgroup>
                         <tr>
                             <td colspan="2">※점수기준</td>
                             <td id="add_data_td" align="right" style="color:blue; cursor: pointer;">[추가]</td>
                         </tr>
                         
                         <?php foreach($safe_points as $safe_point) : ?>

                             <tr class="safe point select">
                                 <td style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= $safe_point['name'] ?></td>
                                 <td align="right"><?= $safe_point['point'] ?><span>점</span></td>
                                 <td align="right" data-id="<?= $safe_point['id'] ?>" class="data_edit"><span style="color:blue; cursor: pointer;">[수정]</span></td>
                             </tr>
                         <?php endforeach ?>

                     </table>
             
                 </div>

                 <div style="width:450px; margin-bottom:16px;">

                     <div style="display:flex; justify-content:space-between; margin-top:18px; margin-bottom:48px">
                         <a href="<?= route_to('view_safe_point', $target_time->subMonths(1)->getTimestamp()) ?>">◀이전달보기</a>
                         <span style="font-size:x-large;"><?= $target_time->getMonth() ?>월</span>
                         <a href="<?= route_to('view_safe_point', $target_time->addMonths(1)->getTimestamp()) ?>">다음달보기▶</a>
                     </div>

                     <table class="ui sortable selectable celled table">
                         <thead class="full-width">
                             <tr align="center">
                                 <th width="50%" style="font-weight:normal;">팀이름</th>
                                 <th width="50%" style="font-weight:normal;">점수</th>
                             </tr>
                         </thead>

                         <tbody>
                             
                             <?php foreach($teams as $team) : ?>

                                 <tr class="safe team select" data-id="<?= $team['id'] ?>" align="center">
                                     <td><?= $team['name'] ?></td>
                                     <td><?= 100 + ($team_safe_points[$team['id']] ?? 0 )?></td>
                                 </tr>
                             <?php endforeach ?>

                         </tbody>
                     </table>
             
                 </div>
             </div>

             
         </div>
     </div>
     
 </form>

 <div id="add_data" class="ui mini modal">
    <div class="content">

        <form id="add_data_form" class="ui form" action="/fm/add_safe_point" method="POST">
            <div class="field">
                <label>추가할 데이터</label>
                <input type="text" name="name" placeholder="기준이름">
                <input type="text" name="point" placeholder="점수">
            </div>
            <div id="add_data_button" style="cursor:pointer;">데이터 추가</div>
        </form>

    </div>
</div>
 
 <div id="edit_data" class="ui mini modal">
    <div class="content">

        <form id="edit_data_form" class="ui form" action="/fm/edit_safe_point" method="POST">
            <div class="field">
                <label>변경할 데이터</label>
                <input id="input_edit_safe_name" type="text" name="name" placeholder="기준이름">
                <input id="input_edit_safe_point" type="text" name="point" placeholder="점수">
            </div>
            <input id="input_edit_safe_point_id" type="hidden" name="id" value="">
            <div id="edit_data_button" style="cursor:pointer;">데이터 수정</div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {

        $('#add_data_td').click(function() {

            $('#add_data').modal('show');

        });

        $('#add_data_button').click(function() {

            $('#add_data_form').submit();

        });

        $('#edit_data_button').click(function() {

            $('#edit_data_form').submit();

        });

        $('td.data_edit').click(function() {

            var prev_name = $(this).prev().prev().text();
            var prev_point = $(this).prev().clone().children().remove().end().text();

            $('#input_edit_safe_name').val(prev_name);

            $('#input_edit_safe_point').val(prev_point);

            $('#input_edit_safe_point_id').val($(this).data('id'));

            $('#edit_data').modal('show');

        });

        $('tr.safe.team.select').click(function() {

            var id = $(this).data('id');

            location.href = '/fm/view_safe_point_team/' + id + '/<?=$target_time->getTimestamp()?>';

        });
        
        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>