var that;
var preCurr=0;
var start_Y=0;
var end_Y=0;
var indexScrollTop=0;
var refreshTimer;
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
    customItem: "allRegion",
    partArr: [{"port":"4987","title":"李克强与欧盟领导人共同会见记者","shortContent":"国务院总理李克强7月16日上午在人民大会堂与欧洲理事会主席图斯克、欧盟委员会主席容克共同会见记者并回答提问。  双方积极评价会晤展现了中国欧...","timestamp":"前天","site":"中国政府网","image":"http:\/\/www.gov.cn\/premier\/2018-07\/16\/5306837\/images\/e34a8feff8044ee9b664c5328937b290.jpg"},{"port":"4986","title":"戏水消暑","shortContent":"7月15日，孩子们在广西柳州市融安县一家水上乐园戏水消暑。 新华社发（谭凯兴 摄）  7月15日，孩子们在广西柳州市融安县一家水上乐园戏水...","timestamp":"前天","site":"新华社","image":"http:\/\/www.gov.cn\/xinwen\/2018-07\/16\/5306850\/images\/6e3383cd7c4a41c0bb456ebda8a41377.jpg"},{"port":"4985","title":"李克强:中欧投资协定谈判进入新阶段","shortContent":"【李克强：中欧投资协定谈判进入新阶段】7月16日上午，李克强总理在同欧洲理事会主席图斯克、欧盟委员会主席容克共同主持第二十次中国欧盟领导人...","timestamp":"前天","site":"中国政府网","image":"http:\/\/www.gov.cn\/premier\/2018-07\/16\/5306878\/images\/b4fb9760ff654ff09df362c96b769d2e.jpg"}],
    reachedTop: true,
    refreshRotate: 0
  },
  onLoad: function () {
    that = this;
    that.setData({currentNum: 0, r_selected: that.data.regions[0]});
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
    that.setData({[currcolorKey]: "black", [curropaKey]: "1", refresh: "", scrollUp: ""})//refresh、scrollUp 用不用 hidden【Boolean】的形式？
  },
  RegionChange: function(e){
    var r_Idx = e.detail.value;
    that.setData({r_selected: that.data.regions[r_Idx], scroll_left: 0})
  },
  Tab: function(e){
    var tabIdx = e.target.dataset.idx;
    that.setData({currentNum: tabIdx});
  },
  loadmore: function(){
    var arrs = [
      {
        port: "5",
        title: "全日本最挑剔的文具大赏，今年选了哪些好文具？", 
        shortContent: "本文首发于微信公众号「Voicer」（voicer_me），少数派经授权转载，仅对排版略作调整。点此阅读原文在日本这个文具大国，相关的奖项数不胜数。既有偏重设计…",
        timestamp: "7月3日",
        site: "中央政府网"
      },
      {
        port: "6",
        title: "如果你现在想买iPad Pro的话，可以等等这款更有性价比的Surface", 
        shortContent: "2018年7月10日，微软在纽约召开媒体发布会，发布了入门级二合一电脑Surface Go。在持续的爆料之后，我们终于见到了这个Surface家族的新成员。它看起来像…",
        timestamp: "7月3日",
        site: "中央政府网"
      }
    ];
    for(var a=0; a<arrs.length; a++){
      var currentPartArr = that.data.partArr;
      for(var a=0; a<arrs.length; a++){
        currentPartArr.push(arrs[a]);
      };
      that.setData({partArr: currentPartArr});
    }
  },
  refresh: function(e){
  },
  scroll: function(e){
    var tempTop = e.detail.scrollTop;
    indexScrollTop = tempTop;
    if(tempTop < 10){
      that.setData({reachedTop: true});
    }else{
      that.setData({reachedTop: false})
    }
    if(tempTop < 300){
      that.setData({scrollUp: ""})
    }
  },
  indexTS: function(e){
    start_Y = e.touches[0].pageY
  },
  indexTE: function(e){
    end_Y = e.changedTouches[0].pageY;
    var shift = end_Y - start_Y
    if(that.data.reachedTop){
      if(shift < 0){
        console.log("上滑")
      }else if(shift > 0){
        console.log("下滑")
        that.setData({refresh: "refresh"});
        refreshTimer = setTimeout(function(){
          that.setData({refresh: ""});
          clearTimeout(refreshTimer)
        }, 1000)
      }
    }else{
      if(shift > 0){
        that.setData({scrollUp: "scrollUp"})
      }else if(shift < 0){
        that.setData({scrollUp: ""})
      }
    }
  },
  scrollToTop: function(){
    console.log(indexScrollTop);
    that.setData({IndexTop: 0});
  }
})
