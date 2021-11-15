<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<form method="POST" name="form">

    <div style="width:fit-content; margin:0 auto; padding:16px;">
        <div class="uiframe" style="width:400px; padding:16px;">

            <i class="arrow left icon" onclick="location.href='/fm'" style="cursor: pointer;"></i>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
                <label>현장</label>
                <div style="width:270px;">
                    <select class="ui fluid dropdown" name="place">
                        <option value="">현장명</option>

                        <?php $old_place = old('place') ?>
                        <?php foreach($places as $place) : ?>

                        <option value="<?=$place['id']?>" <?= $place['id'] == $old_place ? "SELECTED" : "" ?> > <?= $place['name'] ?> </option>

                        <?php endforeach ?>

                    </select>
                </div>

            </div>
            
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                <label>이름</label>
                <input type="text" name="name" value="<?= old('name'); ?>" style="width:270px">
            </div>
            
            <?php 
                $birthday = session()->get('birthday'); 
                $ui_birthday = !is_null($birthday) ? $birthday : "1971-01-01";
                $input_birthday = !is_null($birthday) ? $birthday : "";
            ?>
                        
            <div style="margin-top:16px; margin-bottom:12px;">
                <label style="width:130px">생년월일</label>
                <div class="ui calendar" id="standard_calendar" data-type="date" data-date="<?= $ui_birthday ?>" style="margin-top:8px">
                </div>
                <input type="hidden" name="birthday_calendar" value="<?= $input_birthday ?>">

            </div>

            <div align="right">
                <button class="bluebutton" type="submit" formaction="generate_user" style="width:80px">생성</button>
            </div>

        </div>
    </div>
    
</form>

<?= $this->endSection() ?>

<?= $this->section('custom_js') ?>

    <script type="text/javascript">

        $('#standard_calendar').calendar({
            startMode: 'year',
            type: 'date', 
            text: {
                months: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
                monthsShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            },
            onChange: function(date) {

                year = date.getFullYear().toString();
                month = ('0' + (date.getMonth() + 1)).slice(-2);
                day = ('0' + date.getDate()).slice(-2);

                date_str = year + "-" + month + "-" + day;

                document.form.birthday_calendar.value = date_str;
            }
        });


    </script>

<?= $this->endSection() ?>