/**
 * Created by HeCheng on 2017/7/17.
 */
$(function () {
    $('.list-add .icon-add').click(function () {
        var tr = $('<tr><td><input type="text" name="receivablesCoreEnterpriseId[]" class="border-bottom-input"></td> <td><input name="receivablesCoreEnterpriseName[]" type="text" class="border-bottom-input"></td> <td><input name="receivablesAccountNumber[]" type="text" class="border-bottom-input"></td> <td><input name="openingBank[]" type="text" class="border-bottom-input"></td> </tr>');
        $('.list-add table tbody').append(tr);
    })

    $('.list-content-condition .icon-add').click(function () {
        $('.list-option>section').addClass('list-option-hide').removeClass('list-option-show');
        $('.list-option>form>section').addClass('list-option-show').removeClass('list-option-hide');
    })

    $('#removeFormSubmit,#addFormSubmit').click(function () {
        $('.list-option>section').addClass('list-option-show').removeClass('list-option-hide');
        $('.list-option>form>section').addClass('list-option-hide').removeClass('list-option-show');
    })

    var ajax_option={

        url:"login",//默认是form action

        success:function(data){
            console.log(data);
        }
    }
    $('#addFormSubmit').click(function (e) {
            var options = {
                url: 'index.php',
                type: 'post',
                dataType: 'text',
                data: $("#addForm").serialize(),
                success: function (data) {

                }
            };
            $.ajax(options);
            return false;
    })

    $('#addFormSubmit').click(function () {
        $('.alter-layout').fadeIn();
        var Log = new log();
        Log.init();
        window.Vector = new vector();
        Vector.init();
    })

    $(document).bind('click','body',function (e) {
        if($(e.target).parents('.alter-layout').length == 0){
            $('.alter-layout').fadeOut();
        }
    })
})