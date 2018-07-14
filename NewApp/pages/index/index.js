var that;
var preCurr=0;
Page({
  data: {
    currentNum: 0,
    IndicatorLeft: 0,
    r_index: 0,
    indicatorColor: ["black", "#888", "#888"],
    indicatorShow: ["1", "0", "0"],
    updateTime: "7月12日",
    scroll_left: 0,
    regions: ["北京市", "天津市", "上海市", "重庆市", "河北省", "河南省", "云南省", "辽宁省", "黑龙江省", "湖南省", "安徽省", "山东省", "新疆维吾尔自治区", "江苏省", "浙江省", "江西省", "湖北省", "广西壮族自治区", "甘肃省", "山西省", "内蒙古自治区", "陕西省", "吉林省", "福建省", "贵州省", "广东省", "青海省", "西藏自治区", "四川省", "宁夏回族自治区", "海南省", "台湾省", "香港特别行政区", "澳门特别行政区"],
    customItem: "allRegion"
  },
  onLoad: function () {
    that = this;
    that.setData({currentNum: 1, r_selected: that.data.regions[0]})
  },
  swiped: function(e){
    var currNum = e.detail.current;
    var currcolorKey = `indicatorColor[${currNum}]`;
    var curropaKey = `indicatorShow[${currNum}]`;
    for(var a=0; a<that.data.indicatorShow.length; a++){
      var colorKey = `indicatorColor[${a}]`;
      var opaKey = `indicatorShow[${a}]`;
      that.setData({[colorKey]: "#888", [opaKey]: "0"})
    }
    that.setData({[currcolorKey]: "black", [curropaKey]: "1"})
  },
  RegionChange: function(e){
    console.log(e)
    var r_Idx = e.detail.value;
    that.setData({r_selected: that.data.regions[r_Idx], scroll_left: 0})
  },
  Tab: function(e){
    console.log(e)
    var tabIdx = e.target.dataset.idx;
    that.setData({currentNum: tabIdx});
    
  }
})
