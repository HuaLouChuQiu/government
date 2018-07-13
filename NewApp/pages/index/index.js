var that;
var preCurr=0;
Page({
  data: {
    currentNum: 0,
    IndicatorLeft: 0,
    indicatorColor: ["black", "#888", "#888"],
    indicatorShow: ["1", "0", "0"],
    updateTime: "7月12日"
  },
  onLoad: function () {
    that = this;
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
  Tab: function(e){
    console.log(e)
    var tabIdx = e.target.dataset.idx;
    that.setData({currentNum: tabIdx});
    
  }
})
