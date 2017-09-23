/**
 * Created by HeCheng on 2017/7/17.
 */

$(function () {
/*
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
*/

    var Select = new select();
    Select.init();

    $('.border-light-right>li').click(function () {
        $(this).parents('.sidebar').find('li').removeClass('active');
        $(this).addClass('active');
        if($(this).children('ul').length!=0){
            $(this).find('.icon-xiangxia2').toggleClass('down');
            $(this).children('ul').slideToggle();
        }
    })
})