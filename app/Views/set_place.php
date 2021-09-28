<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px;">
        
        <i class="hamburger icon" onclick="location.href='/fm/menu'" style="cursor: pointer;"></i>

        <div style="height:1px; background-color:#e8e9e9; margin-top:16px; margin-bottom:8px;"></div>

        <table class="ui very basic table" style="margin-top:-8px; margin-bottom:-8px;">
        
            <?php foreach($places as $place) : ?>
                <tr>
                    <td>
                        <div class="ui left pointing dropdown">
                            <?= $place['name'] ?>
                            <div class="menu">
                                <a class="item place_change" data-id="<?= $place['id'] ?>" href="#">현장명 변경</a>
                                <a class="item place_delete" data-id="<?= $place['id'] ?>" href="#">현장기록 삭제</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>

        </table>

        <div style="height:1px; background-color:#e8e9e9; margin-top:8px;"></div>

        <div align="right" style="margin-top:16px;">
            <button class="bluebutton" type="button" id="new_place" style="width:100px">현장 추가</button>
        </div>

        </div>
    </div>
    
</form>
    
<?= $this->endSection() ?>


<?= $this->section('custom_js') ?>

<script type="text/javascript">

    $(document).ready(function() {
        
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

    });

</script>

<?= $this->endSection() ?>
