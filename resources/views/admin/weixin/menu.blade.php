<form action="/admin/weixin/domenu"method="post">
    {{csrf_field()}}
    <div id="div">
        <div style="float:left">
            一级菜单：<input type="text" name="one"><input type="button" value="+" class="btn1">
        </div>
        <div style="float:left">
            二级菜单：<input type="text" name="two"><input type="button" value="+" class="btn2">
        </div>
    </div>

    <input type="submit" value="生成" style="margin:0px 0px 0px 70px">
</form>
<script>
    $(function(){
        var _str="<div>一级菜单：<input type='text' name='one'><input type='button' value='+' class='btn1'>" +
            " 二级菜单：<input type='text' name='two'><input type='button' value='+' class='btn2'></div>";
        $(document).on('click','.btn1',function(){
            var _this=$(this);
            $('#div').append(_str)
        });
    })
</script>