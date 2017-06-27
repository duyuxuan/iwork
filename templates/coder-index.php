<?php $this->layout('layouts/dashboard', ['title' => '技术生产力']) ?>
<?php $this->start('main') ?>

<div class="alert alert-success">
<div class="row">

<div class="col-lg-12">
<form class="form-inline" role="form" method="GET" action="/coder/intime">

<div class="form-group"> <div class="input-group">
<span class="input-group-addon">开始</span>
<input value="<?=$t_start_show?>" name="t_start" class="form-control" type="text" onclick="showcalendar(event, this)">
</div></div>

<div class="form-group"> <div class="input-group">
<span class="input-group-addon">结束</span>
<input value="<?=$t_end_show?>" name="t_end" class="form-control" type="text" onclick="showcalendar(event, this)">
</div></div>

<button class="btn btn-success"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> 查询</button>
</form>
</div>

</div>
</div>


<div class="row"> <div class="col-lg-6">
<table class="table table-bordered table-striped table-hover">
<tr>
<th class="text-center">说明</th>
<th class="text-center">公式</th>
</tr>
<tr>
<th class="text-center">能量分数</th>
<td>ceil( (需求数量 + 改进数量 - 人均数量) / 人均数量 * 100 )</td>
</tr>
<tr>
<th class="text-center">质量分数</th>
<td>100 - ceil(BUG数量 / (需求数量 + 改进数量) * 100) </td>
</tr>
<tr>
<th class="text-center" style="vertical-align:middle">系数</th>
<td>
<p>产出分大于 0 时 = 1 </p>
<p>产出分等于 0 时 = 0 </p>
<p>产出分小于 0 时 = 3 </p>
</td>
</tr>
<tr>
<th class="text-center">综合分数</th>
<td> 产出分 * 系数 + 质量分</td>
</tr>
</table>
</div> </div>

<?php if (isset($coders)) {?>
<div class="row"> <div class="col-lg-6">
    <table class="table table-bordered table-striped table-hover text-center">
      <thead>
        <tr>
<th class="text-center">#id</th>
<th class="text-center">需求</th>
<th class="text-center">改进</th>
<th class="text-center">BUG</th>
<th class="text-center" width="50"> </th>
<th class="text-center">能量分数</th>
<th class="text-center">质量分数</th>
<th class="text-center">综合分数</th>
        </tr>
      </thead>
      <tbody>
<?php
$departments_done = array();
$departments_users = array();
$departments_coder = array();
foreach ($users as $user) {
    if (!isset($coders[$user->id])) {
        continue;
    }
    $one = $coders[$user->id];
    $done = $one[10] + $one[20];
    if (!isset($departments_done[$user->department])) {
        $departments_done[$user->department] = 0;
    }
    if (!isset($departments_users[$user->department])) {
        $departments_users[$user->department] = 0;
    }
    $departments_done[$user->department] += $done;
    $departments_users[$user->department]++;

    if (!isset($departments_coder[$user->department])) {
        $departments_coder[$user->department] = array();
    }

    $departments_coder[$user->department][$user->id] = $one;
}

$arv = array();
foreach ($departments_done as $department => $n) {
    $arv[$department] = ceil($n / $departments_users[$department]);
}
foreach ($departments_coder as $department_id => $department_coder) {
    $arrSort = array();
    foreach ($department_coder as $uid => $one) {
        $done = $one[10] + $one[20];
        $avg = isset($arv[$department_id]) ? $arv[$department_id] : 0;
        $x1 = ceil(($done - $avg) / $avg * 100);
        $one['x1'] = $x1;

        if ($done > 0) {
            $c = ceil($one[30] / $done * 100);
            $x2 = 100 - $c;
        } else {
            $c = 0;
            $x2 = 0;
        }
        $one['x2'] = $x2;
        $tiaozheng = 1;
        if ($x1 < 0) {
            $tiaozheng = 3;
            $one['x3'] = $x1 * 3 + $x2;
        } elseif ($x1 == 0) {
            $tiaozheng = 0;
        }
        $one['x3'] = ceil($x1 * $tiaozheng + $x2);

        $arrSort[$uid] = $one['x3'];

        $department_coder[$uid] = $one;
    }

    arsort($arrSort);
    foreach ($arrSort as $uid => $done) {
        $one = $department_coder[$uid];
?>
<tr>
<td><?=$users[$uid]->name?></td>
<td><?=$one[10]?></td>
<td><?=$one[20]?></td>
<td><?=$one[30]?></td>
<td></td>
<td><?=$one['x1']?></td>
<td><?=$one['x2']?></td>
<td><?=$one['x3']?></td>
</tr>
<?php } } ?>
</tbody>
</table>
</div>
</div>

<?php }?>

<?php $this->end() ?>
