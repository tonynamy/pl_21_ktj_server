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
                             <td id="safepoint_add" align="right" style="color:blue; cursor: pointer;">[추가]</td>
                         </tr>
                         
                         <?php foreach($safe_points as $safe_point) : ?>

                             <tr class="safe point select">
                                 <td style="text-overflow:ellipsis; overflow:hidden; white-space:nowrap;"><?= $safe_point['name'] ?></td>
                                 <td align="right"><?= $safe_point['point'] ?><span>점</span></td>
                                 <td align="right" class="safepoint_edit" data-id="<?= $safe_point['id'] ?>"><span style="color:blue; cursor: pointer;">[수정]</span></td>
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
                                     <td><?= 100 + ($team_safe_points[$team['id']] ?? 0) ?>점</td>
                                 </tr>
                             <?php endforeach ?>

                         </tbody>
                     </table>
             
                 </div>
             </div>

             
         </div>
     </div>
     
 </form>
 
 <!-- 안전점수 기준 추가 modal -->
 <div id="safepoint_add_modal" class="ui mini modal">

    <form id="safepoint_add_form" class="ui form" action="/fm/add_safe_point" method="POST">
        <div style="padding:16px;">

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                <label>기준이름</label>
                <div class="ui input" style="width:250px">
                    <input type="text" name="sp_name" placeholder="이름">
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                <label>점수</label>
                <div class="ui input" style="width:250px">
                    <input type="number" name="sp_point" placeholder="감점은 점수앞에 - 붙혀주세요">
                </div>
            </div>
            <div class="actions" style="text-align:right; margin-top:24px; margin-bottom:4px">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span id="safepoint_add_submit" style="color:#5599DD; cursor:pointer;">추가</span>
            </div>

        </div>
    </form>

</div>

 <!-- 안전점수 기준 수정 modal -->
<div id="safepoint_edit_modal" class="ui mini modal">

    <form id="safepoint_edit_form" class="ui form" action="/fm/edit_safe_point" method="POST">
        <div style="padding:16px;">
        
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px">
                <label>기준이름</label>
                <div class="ui input" style="width:250px">
                    <input id="input_edit_safe_name" type="text" name="sp_name" placeholder="이름">
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px">
                <label>점수</label>
                <div class="ui input" style="width:250px">
                    <input id="input_edit_safe_point" type="number" name="sp_point" placeholder="감점은 점수앞에 - 붙혀주세요">
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                <span id="safepoint_delete_submit" style="color:#5599DD; cursor:pointer;">삭제</span>
                <div class="actions" style="text-align:right;">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="safepoint_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                </div>
            </div>

        </div>
                
        <input id="edit_safe_point_id" type="hidden" name="sp_id">
        <input id="edit_safe_point_is_delete" type="hidden" name="is_delete" value="0">
    </form>

</div>

<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

        
    $(document).ready(function() {

        $('#safepoint_add').click(function() {

            $('#safepoint_add_modal').modal('show');

        });

        
        $('#safepoint_add_submit').click(function() {

            $('#safepoint_add_form').submit();

        });

        $('.safepoint_edit').click(function() {

            var prev_name = $(this).prev().prev().text();
            var prev_point = $(this).prev().clone().children().remove().end().text();

            $('#input_edit_safe_name').val(prev_name);
            $('#input_edit_safe_point').val(prev_point);
            $('#edit_safe_point_id').val($(this).data('id'));
            
            $('#safepoint_edit_modal').modal('show');
        });

        $('#safepoint_edit_submit').click(function() {

            $('#safepoint_edit_form').submit();

        });

        $('#safepoint_delete_submit').click(function() {

            $('#edit_safe_point_is_delete').val(1);
            $('#safepoint_edit_form').submit();

        });

        //팀 클릭시 해당 팀으로 이동
        $('tr.safe.team.select').click(function() {

            var id = $(this).data('id');

            location.href = '/fm/view_safe_point_team/' + id + '/<?= $target_time->getTimestamp() ?>';

        });
        
        $('table').tablesort();

    });

</script>

<?= $this->endSection() ?>