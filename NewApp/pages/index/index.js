var App = getApp();
var that;
var preCurr=0;
var start_Y=0;
var end_Y=0;
var indexScrollTop=0;
var refreshTimer;
var indexLastPort = 0;
var lastPartArr = [];
var arrs = [];
var NumPerload = 3;
Page({
  data: {
    _show: "",
    theEmerBoolean: true,
    netError: false,
    _NickName: "",
    _Profile: "../../images/photo.svg",
    completeBoolean: true,
    IndexColor: "#fff",
    loadmore: "loadmore",
    scrollToTop: "scrollToTop",
    currentNum: 0,
    IndicatorLeft: 0,
    r_index: 0,
    indicatorColor: ["black", "#888", "#888"],
    indicatorShow: ["1", "0", "0"],
    updateTime: "7月12日",
    scroll_left: 0,
    regions: ["北京市", "天津市", "上海市", "重庆市", "河北省", "河南省", "云南省", "辽宁省", "黑龙江省", "湖南省", "安徽省", "山东省", "新疆维吾尔自治区", "江苏省", "浙江省", "江西省", "湖北省", "广西壮族自治区", "甘肃省", "山西省", "内蒙古自治区", "陕西省", "吉林省", "福建省", "贵州省", "广东省", "青海省", "西藏自治区", "四川省", "宁夏回族自治区", "海南省", "台湾省", "香港特别行政区", "澳门特别行政区"],
    customItem: "allRegion",
    partArr: [],
    reachedTop: true,
    refreshRotate: 0,
    IndexWindowOptions: {
      CleanHistory: {
        surface: {
          _header: "清空阅读记录",
          _body: "确认清空阅读历史记录吗？",
          _okBtn: true,
          _noBtn: true
        },
        function: {
          _OK: "_OK_cleanHistory",
          _CANCEL: "_CANCEL_cleanHistory"
        }
      }
    }
  },
  onLoad: function () {
    that = this;
    that.setData({currentNum: 2, r_selected: that.data.regions[0]});
    wx.getStorage({
      key: "prevPartArr",
      success: function(prev){
        that.setData({partArr: prev.data, completeBoolean: false});
        console.log(prev.data)
      },
      fail: function(){
        wx.request({
          url: "https://yixinping.top/government/api/index?c=index_news&m=getState_news",
          success: function(newFour){
            that.setData({partArr: newFour.data});
            lastPartArr = that.data.partArr;
            indexLastPort = lastPartArr[lastPartArr.length - 1].port;
            wx.setStorageSync("prevPartArr", newFour.data);
          },
          complete: function(){
            that.setData({completeBoolean: false, IndexColor: "#eee"})
          }
        });
      }
    })
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
    if(currNum == 2){
      wx.getStorage({
        key: "getUserInfoFailed",
        success: function(theBool){
          if(theBool){
            that.setData({theEmerBoolean: true});
          }else{
            that.setData({theEmerBoolean: false});
            that.setData({_NickName: App.globalData.userInfo.nickName});
            if(App.globalData.hasNet){
              that.setData({_Profile: App.globalData.userInfo.avatarUrl})
            }
          }
        }
      })
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
    // console.log(indexLastPort);
    that.setData({loadmore: "void", loadUp: "scrollUp loadUp"});
    if(that.data.netError){that.onLoad()}
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news&p1=${indexLastPort}&p2=${NumPerload}`,
      success: function(loadTwo){
        that.setData({netError: false});
        // console.log(loadTwo);
        that.setData({loadUp: ""})
        arrs = loadTwo.data;
        if(arrs.length < NumPerload){
          console.log("已到底");
          that.setData({reached: "reached", scrollUp: "scrollUp"})
        }
        for(var a=0; a<arrs.length; a++){
          var currentPartArr = that.data.partArr;
          for(var a=0; a<arrs.length; a++){
            currentPartArr.push(arrs[a]);
          };
          that.setData({partArr: currentPartArr});
        };
          indexLastPort = that.data.partArr[that.data.partArr.length-1].port;
          // console.log("loadmore", that.data.partArr[that.data.partArr.length-1].port);
          that.setData({loadmore: "loadmore"});
      },
      fail: function(){
        that.setData({netError: true})
      }
    })
  },
  void: function(){},
  refresh: function(e){
    that.onLoad();
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
      }else if(shift > 10){
        console.log("下滑")
        that.setData({refresh: "refresh", loadUp: ""});
        refreshTimer = setTimeout(function(){
          that.setData({refresh: ""});
          clearTimeout(refreshTimer)
        }, 1000);
        that.refresh();
      }
    }else{
      if(shift > 0){
        that.setData({scrollUp: "scrollUp", reached: "", loadmore: "loadmore"})
      }else if(shift < 0){
        that.setData({scrollUp: ""})
      }
    }
  },
  scrollToTop: function(){
    console.log(indexScrollTop);
    that.setData({IndexTop: 0});
  },
  LoadPassage: function(e){
    console.log(e.currentTarget.dataset.port);
    wx.navigateTo({
      url: "../passages/passages?" + `port=${e.currentTarget.dataset.port}`
    })
  },
  TurnToHistory: function(){
    wx.navigateTo({
      url: "../history/history"
    })
  },
  clearHistory: function(){
    that.setData({OPTIONNAME: that.data.IndexWindowOptions.CleanHistory, _show: "_show"})
  },
  _OK_cleanHistory: function(){
    that._closeWindow();
    wx.removeStorageSync("historyPartContent");
    wx.removeStorageSync("history");
  },
  _CANCEL_cleanHistory: function(){
    that.setData({_show: ""})
  },
  _closeWindow: function(){
    that.setData({_show: ""})
  }
});
