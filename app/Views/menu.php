<?= $this->extend('common_layout') ?>

<?= $this->section('content') ?>

<table class="ui definition table">
  <thead>
    <tr><th></th>
    <th>메뉴 이동</th>
  </tr></thead>
  <tbody>
    <tr>
        <td>등록</td>
        <td>
            <div class="ui buttons">
                <a href="/register_team" class="ui button">팀 등록</a>
                <button class="ui button">도면 등록</button>
            </div>
        </td>
    </tr>
    <tr>
        <td>등록</td>
        <td>
            <div class="ui buttons">
                <button class="ui button">출퇴근 조회</button>
                <button class="ui button">현장 조회</button>
            </div>
        </td>
    </tr>
</tbody></table>


<?= $this->endSection() ?>