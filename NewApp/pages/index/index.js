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
    partArr: [
      {
        port: "1",
        title: "视觉风格仍在调整，多任务切换操作进一步优化：Android P DP4 更新 | 具透", 
        shortContent: "昨天凌晨 Google 例行向 Pixel 和 Pixel 2 设备放出了 7 月安全更新，更重要的是，原计划于 6 月下旬公布的第四个 Android P 开发者预览版（下文简称 DP4）在短暂的跳...",
        timeStamp: "7月4日",
        site: "中央政府网",
        image: "https://mp.weixin.qq.com/debug/wxadoc/dev/image/cat/0.jpg?t=2018712"
      },
      {
        port: "2",
        title: "AutoWake，手腕上的智能闹钟", 
        shortContent: "下面这些情况，我相信不少拿手机当闹钟的朋友应该会经常碰到：早上睡得正香，被整点闹钟惊醒。相信这对于很多人来说就是「痛苦」一天的开始，从早到晚昏沉的…",
        timeStamp: "7月3日",
        site: "中央政府网"
      },
      {
        port: "3",
        title: "这款脑洞大开的魔性小游戏，让你在经典浮世绘里玩冲浪：UkiyoWave", 
        shortContent: "基于艺术的美感结合娱乐的新奇打造而成的创新游戏，往往能给人留下深刻印象也让人爱不释手。将波洛克泼墨和弹球相结合的、将年画装进找茬的年画找不同、…INKS",
        timeStamp: "7月3日",
        site: "中央政府网"
      },
      {
        port: "4",
        title: "喜欢的图片不适合做壁纸？用这款App来搞定：MagicArt Pro|App+1", 
        shortContent: "少数派曾为大家推荐了不少壁纸类App，如克拉壁纸WLPPR、和Cuto等等。借助它们，你可以轻易找到并更换美观、适合自己设备的壁纸。尽管如此，我们有时还是…",
        timeStamp: "7月3日",
        site: "中央政府网"
      }
    ],
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
  },
  loadmore: function(){
    var arrs = [
      {
        port: "5",
        title: "全日本最挑剔的文具大赏，今年选了哪些好文具？", 
        shortContent: "本文首发于微信公众号「Voicer」（voicer_me），少数派经授权转载，仅对排版略作调整。点此阅读原文在日本这个文具大国，相关的奖项数不胜数。既有偏重设计…",
        timeStamp: "7月3日",
        site: "中央政府网"
      },
      {
        port: "6",
        title: "如果你现在想买iPad Pro的话，可以等等这款更有性价比的Surface", 
        shortContent: "2018年7月10日，微软在纽约召开媒体发布会，发布了入门级二合一电脑Surface Go。在持续的爆料之后，我们终于见到了这个Surface家族的新成员。它看起来像…",
        timeStamp: "7月3日",
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
    console.log(that.data.reachedTop)
  },
  indexTS: function(e){
    console.log(e.touches[0].pageY)
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
        }, 3000)
      }
    }else{
      if(shift > 0){
        console.log("xiahua")
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
