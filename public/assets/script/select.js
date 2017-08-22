/**
 * Created by HeCheng on 2017/8/21.
 */
function select(){

    this.range = $('.list-table td'); // 点击的范围
    this.toggle = false; //鼠标是否按下
    this.selectRange = ''; //字符串格式的总数据

    this.initSelect = null; //鼠标按下时选择的区域
    this.x1 = null;
    this.y1 = null;
    this.x2 = null;
    this.y2 = null;

    var _self = this;

    this.init = function () {
        this.setEvent();
    }

    this.setEvent = function () {
        this.range.mousedown(function (e) {
            e.preventDefault();
            if($(this).index()==0)return false;

            _self.toggle = true;
            _self.initSelect = $(this);
            _self.x1 = _self.initSelect.index();
            _self.y1 = _self.initSelect.parent().index();
        })

        this.range.mouseover(function (e) {
            e.preventDefault();
            if(!_self.toggle){
                $(this).parent().addClass('active').siblings().removeClass('active');
                return false;
            }

            _self.clearSelect();
            _self.x2 = $(this).index();
            _self.y2 = $(this).parent().index();

            var selectRange = _self.createStrOne(_self.x1,_self.y1,_self.x2,_self.y2);
            _self.setSelect(selectRange);
        });

        this.range.click(function () {
        })

        $('.list-table tbody').mouseout(function () {
            $('.list-table tbody tr').removeClass('active');
        })

        $(document).mouseup(function (e) {
            if(!_self.toggle) return;
            /**
             * 合并并更新选中范围字符串
             * 对选中的td进行计算
             **/
            if(_self.selectRange)_self.selectRange+='|';
            if(_self.x1!==null&&_self.y1!==null&&_self.x2!==null&&_self.y2!==null){
                _self.selectRange += 'x'+_self.x1;
                _self.selectRange += ',';
                _self.selectRange += 'y'+_self.y1;
                _self.selectRange += ' ';
                _self.selectRange += 'x'+_self.x2;
                _self.selectRange += ',';
                _self.selectRange += 'y'+_self.y2;
            }else if(_self.x1!==null&&_self.y1!==null&&_self.x2===null&&_self.y2===null){
                _self.selectRange += 'x'+_self.x1;
                _self.selectRange += ',';
                _self.selectRange += 'y'+_self.y1;
            }
            $("#range").val(_self.selectRange);
            _self.moveEnd('range');
            _self.updateSelectLast();

            _self.clearSelect();
            _self.clearSite();
            _self.toggle = false;
        })

        /* 重置所有操作 */
        $('.btn.btn-primary.btn-lg.ripple.reset').click(function () {
            _self.reset();
        })
    }

    this.reset = function () {
        _self.clearSelect();
        _self.clearSite();
        _self.selectRange = ''; //字符串格式的总数据
        $('.list-table tbody td').removeClass('select-error')
        $('.list-table tbody td').removeClass('select-right')
        $('.list-control.info').children('li').eq(1).html('共：');
        $('.list-control.info').children('li').eq(2).html('买：');
        $('.list-control.info').children('li').eq(3).html('卖：');
        $('.list-control.info').children('li').eq(4).html('荐：');
        $('#range').val('');
    }

    this.createStrOne = function (x1,y1,x2,y2) {
        var minX = Math.min(x1,x2);
        minX = Math.max(minX,1); //不能选中坐标
        var maxX = Math.max(x1,x2);
        var minY = Math.min(y1,y2);
        var maxY = Math.max(y1,y2);
        return {
            minX : minX,
            minY : minY,
            maxX : maxX,
            maxY : maxY,
        }
    }

    /**
     * 根据obj的范围对选中元素添加样式
     * obj 为createStrOne所返回的范围
     * bool为是否根据内部值调整颜色
     **/
    this.setSelect = function (obj,bool) {
        for (var i=obj.minY; i<=obj.maxY; i++){
            for (var j=obj.minX; j<=obj.maxX ; j++){
                var td = $('.list-table tbody').children('tr:eq('+i+')').children('td:eq('+j+')');
                if(bool){
                    var num = td.html();
                    var num = num.replace('%','');
                    if(num<0){
                        td.addClass('select-error');
                    }else{
                        td.addClass('select-right');
                    }
                }else{
                    td.addClass('active');
                }
            }
        }
    }

    /* 读取字符串更新最后的样式区域 */
    this.updateSelectLast = function () {
        var preg = /(x\d+,y\d+ x\d+,y\d+|x\d+,y\d+)((\|x\d+,y\d+ x\d+,y\d+|\|x\d+,y\d+)*)/;
        if(!_self.selectRange.match(preg)){
            alert('请输入正确的格式');
            return;
        }

        /*3,y1 x6,y6|x2,y0 x3,y3|x7,y1*/
        var selectRangeArr = _self.selectRange.split('|');

        /* x7,y1 */
        var selectRange = selectRangeArr[selectRangeArr.length-1];

        /* 更新样式 */
        this.updateSelectOne(selectRange);

        /* 计算当前样式的对应数据 */
        this.setRecommend();
    }

    /**
     * 参数: x1,y1 或
     *      x1,y1 x2,y2
     * 操作：更新该区域内的数据
     **/
    this.updateSelectOne = function (str) {
        var arr = str.split(' ');
        if(arr.length==1){
            /* 只点了一下的操作 */
        }else if(arr.length==2){
            /* 选择了范围的操作 */
            var xy1 = arr[0].split(',');
            var x1 = this.removeStr(xy1[0]);
            var y1 = this.removeStr(xy1[1]);
            var xy2 = arr[1].split(',');
            var x2 = this.removeStr(xy2[0]);
            var y2 = this.removeStr(xy2[1]);
            var selectRange = this.createStrOne(x1,y1,x2,y2);
            this.setSelect(selectRange,true);
        }
    }

    /**
     * 根据class计算出对应的数据
     **/
    this.setRecommend = function () {
        /**
         * 买，卖，共
         **/
        var buy = $('.list-table td.select-right').length;
        var sell = $('.list-table td.select-error').length;
        var num = buy+sell;
        var rc = buy-sell>=3? '做多':'空';

        $('.list-control.info').children('li').eq(1).html('共：'+num);
        $('.list-control.info').children('li').eq(2).html('买：'+buy);
        $('.list-control.info').children('li').eq(3).html('卖：'+sell);
        $('.list-control.info').children('li').eq(4).html('荐：'+rc);
    }

    /* 清除所有激活的active样式 */
    this.clearSelect = function () {
        $('.list-table tbody td').removeClass('active')
        $('.list-table tbody tr').removeClass('active');
    }

    /* 清除所有数据 */
    this.clearSite = function () {
        this.initSelect = null; //鼠标按下时选择的区域
        this.x1 = null;
        this.y1 = null;
        this.x2 = null;
        this.y2 = null;
    }
    
    /* 移动光标到末尾 */
    this.moveEnd = function (id){
        var range, el = document.getElementById(id);
        if (el.setSelectionRange) {
            el.focus();
            el.setSelectionRange(el.value.length, el.value.length)
        } else {
            range = el.createTextRange();
            range.collapse(false);
            range.select();
        }
    }

    /* 自动去除xy */
    this.removeStr = function (str) {
        str = str.replace('x','');
        str = str.replace('y','');
        str = str.replace('%','');
        return str;
    }
}
