<form>
    <table border="1">
        <?php foreach($list as $k=>$v){ ?>
        <tr>
            <td><input type="checkbox" class="btn" value="<?php echo $v['id']?>"></td>
            <td><?php echo $v['id']?></td>
            <td><?php echo $v['openid']?></td>
            <td><?php echo $v['nickname']?></td>
            <td><?php echo $v['sex']?></td>
            <td><?php echo '<img src='.$v['headimgurl'].'>'?></td>
            <td><a href="/weixin/lahei/<?php echo $v['id']?>">添加黑名单</a></td>
        </tr>
            <?php }?>
            <a href="/weixin/getusertag">添加标签</a>
    </table>
</form>