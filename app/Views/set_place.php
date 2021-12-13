<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

    <form method="POST">

        <div style="width:fit-content; margin:0 auto; padding:16px;">
            <div class="uiframe" style="width:400px;">

                <div style="padding:16px">
                    <i class="bars icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>
                    <label>현장 관리</label>
                </div>

                <div style="height:1px; background-color:#e8e9e9;"></div>

                <table width="100%" style="padding-top:8px">

                    <?php foreach($places as $place) : ?>
                        <tr class="place_edit" data-id="<?= $place['id'] ?>" data-name="<?= $place['name'] ?>" data-hide="<?= $place['is_hide'] ?>" style="cursor:pointer;">
                            <td>
                                <!-- div class="ui left pointing dropdown">
                                    <?= $place['name'] ?>
                                    <div class="menu">
                                        <a class="item place_change" data-id="<?= $place['id'] ?>" href="#">현장명 변경</a>
                                        <a class="item place_delete" data-id="<?= $place['id'] ?>" href="#">현장기록 삭제</a>
                                    </div>
                                </div -->
                                <div style="display:flex; justify-content:space-between; align-items:center; padding-left:16px; padding-right:16px">
                                    <div style="padding-top:8px; padding-bottom:8px;"><?= $place['name'] ?></div>
                                    
                                    <?php if($place['is_hide'] == 1) : ?>
                                        <i class="eye slash icon"></i>
                                    <?php endif ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </table>

                <div align="right" style="padding:16px;">
                    <button id="place_add" class="bluebutton" type="button" style="width:100px">현장 추가</button>
                </div>

            </div>
        </div>
        
    </form>
             
    <!-- 현장수정 modal -->
    <div id="place_edit_modal" class="ui mini modal" style="padding:16px;">

        <form id="place_edit_form" class="ui form" method="POST" action="/fm/edit_place">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <label class="data_name">현장명</label>
                <input type="text" name="data" placeholder="수정할 현장명을 입력하세요." style="width:270px;">
            </div>
            <div class="ui checkbox" style="margin-bottom:8px;">
                <input type="checkbox" name="is_hide">
                <label style="font-weight:normal">현장 숨기기</label>
            </div>

            <div style="display:flex; justify-content:space-between; margin-top:24px; margin-bottom:4px">
                <span id="place_delete_submit" style="color:#5599DD; cursor:pointer;">현장 삭제</span> 
                <div class="actions" style="text-align:right;">
                    <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                    <span id="place_edit_submit" style="color:#5599DD; cursor:pointer;">수정</span>
                </div>
            </div>
            
            <input type="hidden" name="place_id">
            <input type="hidden" name="is_delete">
        </form>
    </div>

    <!-- 현장추가 modal -->
    <div id="place_add_modal" class="ui mini modal" style="padding:16px;">

        <form id="place_add_form" class="ui form" method="POST" action="/fm/edit_place">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
                <label class="data_name">현장명</label>
                <input type="text" name="data" placeholder="추가할 현장명을 입력하세요." style="width:270px;">
            </div>

            <div class="actions" style="text-align:right;">
                <span class="cancel" style="color:#5599DD; cursor:pointer; margin-right:32px;">취소</span>
                <span id="place_add_submit" style="color:#5599DD; cursor:pointer;">추가</span>
            </div>
        </form>
    </div>
    
<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
        //prompt 방식
        $('.item.place_change').click(function() {

            var id = $(this).data('id');

            var new_name = prompt('바꿀 이름을 입력하세요.');

            if(new_name != null) {

                var newForm = $('<form></form>');

                newForm.attr("name", "newForm");
                newForm.attr("method", "post");
                newForm.attr("action", "/fm/change_place_name");
                
                newForm.append($('<input/>', {type: 'hidden', name: 'id', value: id }));
                newForm.append($('<input/>', {type: 'hidden', name: 'new_name', value: new_name }));

                newForm.appendTo('body');
                newForm.submit();
            }
        });

        //prompt 방식
        $('#new_place').click(function() {

            var place_name = prompt('현장명을 입력하세요.');

            if(place_name != null) {

                var newForm = $('<form></form>');

                newForm.attr("name", "newForm");
                newForm.attr("method", "post");
                newForm.attr("action", "/fm/change_place_name");
                
                newForm.append($('<input/>', {type: 'hidden', name: 'new_name', value: place_name }));

                newForm.appendTo('body');
                newForm.submit();
            }
        });

        $('#place_add').click(function() {

            $('#place_add_modal').modal('show');

        });
        $('#place_add_submit').click(function() {

            $('#place_add_form').submit();

        });

        
        $('.place_edit').click(function() {

            var id = $(this).data('id');
            var name = $(this).data('name');
            var hide = $(this).data('hide');

            $('#place_edit_form [name=place_id]').val(id);
            $('#place_edit_form [name=data]').val(name);

            if(hide == 1) {
                $('#place_edit_form [name=is_hide]').prop('checked', true);

            } else {
                $('#place_edit_form [name=is_hide]').prop('checked', false);

            }

            $('#place_edit_modal').modal('show');

        });
        $('#place_edit_submit').click(function() {

            $('#place_edit_form [name=is_delete]').val(false);
            $('#place_edit_form').submit();

        });
        $('#place_delete_submit').click(function() {

            $('#place_edit_form [name=is_delete]').val(true);
            $('#place_edit_form').submit();

        });

    });

</script>

<?= $this->endSection() ?>
