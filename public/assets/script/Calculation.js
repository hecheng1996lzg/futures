/**
 * Created by HeCheng on 2017/7/2.
 */
function Calculation() {
    var results = {};

    var minYear = 2012; //最低年份

    var data2 = []; //转换2维数组，并进行年份删选
    var float_continuity = []; //近期均线的上下浮动

    this.calculationStart = function (data,max_average,max_continuity) {
        for(var i=1; i<=max_average; i++){
            for(var j=2; j<=max_continuity; j++){
                results[i] = {};
                results[i][j] = this.calculationCount(data,i,j);
            }
        }
        return results;
    }

    this.calculationCount = function (data,average,continuity) {
        for(var i=0; i<data.length; i++){
            console.log(data[i]);
            /*var row = data[i].splice(' ');
            /!* 年份日期格式验证 *!/
            var date = data[i][0].split("-");
            console.log(date);*/
        }
    }

}