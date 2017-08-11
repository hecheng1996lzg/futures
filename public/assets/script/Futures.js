/**
 * Created by HeCheng on 2017/7/1.
 */

function Futures() {
    Calculation.call(this);

    window.futures = this;

    var max_average = 51;
    var max_continuity = 10;

    this.init= function () {
        $('.results').html('');
        this.setEvent();
    }
    
    this.setEvent= function () {
        $('#form-futures').submit(function (e) {
            e.preventDefault();
            var file = document.getElementById("file").files[0];
            var reader = new FileReader();

            //将文件以文本形式读入页面
            reader.readAsText(file,"GBK");
            reader.onload=function(f){
                var fileText = this.result;
                fileText = fileText.split("\r\n");

                var results = futures.calculationStart(fileText,max_average,max_continuity);
                //console.log(results);
            }
        })
    }
}

$(function () {
    var futures = new Futures();
    //futures.init();
})
