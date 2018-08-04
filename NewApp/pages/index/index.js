var App = getApp();
var commonSettings = require("../../AppSeetings.js");
var that;
var preCurr=0;
var start_Y=0;
var end_Y=0;
var indexScrollTop=0;
var _indexScrollTop=0;
var refreshTimer;
var indexLastPort = 0;
var _indexLastPort = 0;
var lastPartArr = [];
var arrs = [];
var NumPerload = 3;
var theTempCurrent = 0;
Page({
  data: {
    prev_Step: "prev_Step",
    next_Step: "next_Step",
    next_complete: "下一步",
    _P_current: 0,
    _P_close: "_P_close",
    _show: "",
    theEmerBoolean: true,
    netError: false,
    _NickName: "",
    _Profile: "../../images/photo.svg",
    completeBoolean: true,
    _completeBoolean: true,
    IndexColor: "#fff",
    loadmore: "loadmore",
    _loadmore: "_loadmore",
    scrollToTop: "scrollToTop",
    currentNum: 0,
    IndicatorLeft: 0,
    r_index: 0,
    indicatorColor: ["black", "#888", "#888"],
    indicatorShow: ["1", "0", "0"],
    updateTime: "7月12日",
    scroll_left: 0,
    regions: ["北京市", "天津市", "上海市", "重庆市", "河北省", "河南省", "云南省", "辽宁省", "黑龙江省", "湖南省", "安徽省", "山东省", "新疆维吾尔自治区", "江苏省", "浙江省", "江西省", "湖北省", "广西壮族自治区", "甘肃省", "山西省", "内蒙古自治区", "陕西省", "吉林省", "福建省", "贵州省", "广东省", "青海省", "西藏自治区", "四川省", "宁夏回族自治区", "海南省", "台湾省", "香港特别行政区", "澳门特别行政区"],
    _P_words: [
      {word: "经济", color: "#fbc02d", BorderColor: "#fbc02d", fill: "none"},
      {word: "医疗", color: "#e51400", BorderColor: "#e51400", fill: "none"},
      {word: "养老", color: "#26a69a", BorderColor: "#26a69a", fill: "none"},
      {word: "教育", color: "#42a5f5", BorderColor: "#42a5f5", fill: "none"},
      {word: "住房", color: "#c5c5c5", BorderColor: "#c5c5c5", fill: "none"},
      {word: "环境", color: "#8cdcf0", BorderColor: "#8cdcf0", fill: "none"},
      {word: "办事难", color: "#c5c5c5", BorderColor: "#c5c5c5", fill: "none"},
      {word: "脱贫", color: "#fbc02d", BorderColor: "#fbc02d", fill: "none"},
      {word: "三农", color: "#26a69a", BorderColor: "#26a69a", fill: "none"}
    ],
    _P_Boolean: [false, false, false, false, false, false, false, false, false],
    customItem: "allRegion",
    partArr: [],
    reachedTop: true,
    _reachedTop: true,
    refreshRotate: 0,
    IndexWindowOptions: {
      CleanHistory: {
        surface: {
          _header: "清空阅读记录",
          _body: "确认清空阅读历史记录吗？",
          _okBtn: "确定",
          _noBtn: "取消",
          GETUSERINFO: false
        },
        function: {
          _OK: "_OK_cleanHistory",
          _CANCEL: "_CANCEL_cleanHistory"
        }
      },
      enterWeekly: {
        surface: {
          _header: "用户授权",
          _body: "周报功能需要获取您的个人信息，以方便记下您的偏好，为您准确推送内容，确认继续？",
          _okBtn: "继续",
          _noBtn: "取消",
          GETUSERINFO: true
        },
        function: {
          _OK: "_OK_enterWeekly",
          _CANCEL: "_CANCEL_enterWeekly"
        }
      }
    }
  },
  onLoad: function () {
    that = this;
    console.log(commonSettings)
    console.log(commonSettings.commonSettings)
    if(!wx.getStorageSync("firstTime")){
      that.setData({_P_close: ""});
    }
    that.setData({currentNum: 0, r_selected: that.data.regions[0]});
    wx.getStorage({
      key: "prevPartArr",
      success: function(prev){
        commonSettings.endowFontStyle(that, "part", "PARTTITLEFONT");
        that.setData({partArr: prev.data, completeBoolean: false});
      },
      fail: function(){
        var _fontStyleArr = wx.getStorageSync("FontSizeBool");
        var SizeNum = 0;
        for(var a=0; a<_fontStyleArr.length; a++){
          if(_fontStyleArr[a]) SizeNum = a;
        }
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news&p1=0&p2=6&p=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}`,
          success: function(newFour){
            if(wx.getStorageSync("FontSizeBool")){
              commonSettings.endowFontStyle(that, "part", "PARTTITLEFONT")
            }else{
              var newFontSize = [true, false, false];
              wx.setStorageSync("FontSizeBool", newFontSize);
              commonSettings.endowFontStyle(that, "part", "PARTTITLEFONT")
            }
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
    });
    that.LoadPrefer();
  },
  LoadPrefer: function(){
    that._refresh();
  },
  // endowFontStyle: function(nameData){
  //   commonSettings.endowFontStyle(nameData);
  // },
  close_Window: function(){
    that.setData({_P_close: "_P_close"});
    wx.setStorageSync("firstTime", true);
  },
  next_Step: function(){
    wx.setStorageSync("firstTime", true);
    that.setData({_P_current: 1, extend: "extend", next_Step: "_next_Step", prev_Step: "prev_Step", next_complete: "下一步"})
  },
  _next_Step: function(){
    that.setData({_P_current: 2, next_complete: "完成", next_Step: "_P_complete", prev_Step: "_prev_Step"});
    var emptyArr = [];
    var selectArr = that.data._P_Boolean;
    for(var y=0; y<selectArr.length; y++){
      if(selectArr[y]){
        emptyArr.push(y)
      }
    }
    var selected_P_words = [];
    for(var z=0; z<emptyArr.length; z++){
      var tempWordItem = that.data._P_words[emptyArr[z]];
      selected_P_words.push(tempWordItem);
    }
    that.setData({selected_P_words: selected_P_words, wordsNum: selected_P_words.length})
  },
  _prev_Step: function(){
    that.setData({_P_current: 1, next_Step: "_next_Step", prev_Step: "prev_Step", next_complete: "下一步"})
  },
  prev_Step: function(){
    that.setData({_P_current: 0, extend: "", next_Step: "next_Step", next_complete: "下一步"})
  },
  _P_swiped: function(e){
    var Tempcurrent = e.detail.current;
    if(Tempcurrent - theTempCurrent<0){
      if(Tempcurrent == 1){
        that._prev_Step();
      }else if(Tempcurrent == 0){
        that.prev_Step();
      }
    }else{
      if(Tempcurrent == 1){
        that.next_Step();
      }else if(Tempcurrent == 2){
        that._next_Step();
      }
    }
    theTempCurrent = Tempcurrent;
  },
  _P_complete: function(){
    var selectedFormBool = that.data._P_Boolean;
    var selectedFormJson = that.data._P_words;
    if(wx.getStorageSync("userPreferenceBool")){
      var userIds = wx.getStorageSync("userIds");
      wx.request({
        url: `https://yixinping.top/government/api/index?c=Wechat_login&m=chang_hobby&p1=${userIds.id}&p2=${userIds.openid}&p3=${JSON.stringify(selectedFormBool)}`,
        success: function(res){
          console.log(res);
        }
      })
    }
    wx.setStorageSync("userPreferenceBool", selectedFormBool);
    wx.setStorageSync("userPreferenceJson", selectedFormJson);
    that.setData({_P_close: "_P_close"});
  },
  enterPreference: function(){
    theTempCurrent = 1;
    if(wx.getStorageSync("userPreferenceJson")){
      that.setData({_P_words: wx.getStorageSync("userPreferenceJson")});
      that.setData({_P_Boolean: wx.getStorageSync("userPreferenceBool")});
    }
    that.setData({_P_close: "", extend: "extend", _P_current: 1, next_Step: "_next_Step", prev_Step: "prev_Step", next_complete: "下一步"})
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
      if(wx.getStorageSync("print_Arr")){
        that.setData({print_time: wx.getStorageSync("print_Arr").length});
      }else{
        that.setData({print_time: 0});
      }
      wx.getStorage({
        key: "userInfo",
        success: function(userInfo){
          that.setData({theEmerBoolean: false, _Profile: userInfo.data.avatarUrl, _NickName: userInfo.data.nickName});
          console.log("用户已授权，本地存在userInfo")
        },
        fail: function(){
          that.setData({theEmerBoolean: true});
          console.log("用户未授权，本地不存在userInfo")
        }
      });
    }else if(currNum == 0){
      commonSettings.endowFontStyle(that, "part", "PARTTITLEFONT");
    }else if(currNum == 1){
      commonSettings.endowFontStyle(that, "part", "_PARTTITLEFONT");
    }
    that.setData({[currcolorKey]: "black", [curropaKey]: "1", refresh: "", scrollUp: ""})//refresh、scrollUp 用不用 hidden【Boolean】的形式？
  },
  _P_word_select: function(e){
    var _P_Idx = e.currentTarget.dataset.idx;
    var _P_select = e.currentTarget.dataset.select;
    var tempKey_color = `_P_words[${_P_Idx}].color`;
    var tempKey_fill = `_P_words[${_P_Idx}].fill`;
    var booleanKey = `_P_Boolean[${_P_Idx}]`;
    if(!that.data._P_Boolean[_P_Idx]){
      that.setData({[tempKey_color]: "#fff", [tempKey_fill]: _P_select.color, [booleanKey]: true});
    }else{
      that.setData({[tempKey_color]: that.data._P_words[_P_Idx].BorderColor, [tempKey_fill]: "none", [booleanKey]: false})
    }
  },
  TURNtoCURR2: function(){
    wx.getStorage({
      key: "userInfo",
      success: function(){
        that.enterWeekly();
      },
      fail: function(){
        that.setData({currentNum: 2, bling: "bling"});
        var temptimer = setTimeout(function(){
          that.setData({bling: ""});
          clearTimeout(temptimer);
        }, 3000);
      }
    })
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
    var fontStyleArr = wx.getStorageSync("FontSizeBool");
    var SizeNum = 0;
    for(var a=0; a<fontStyleArr.length; a++){
      if(fontStyleArr[a]) SizeNum = a;
    }
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news&p1=${indexLastPort}&p2=${NumPerload}&p=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}`,
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
  _loadmore: function(){
    that.setData({_loadmore: "void", loadUp: "scrollUp loadUp"});
    var fontStyleArr = wx.getStorageSync("FontSizeBool");
    var SizeNum = 0;
    for(var a=0; a<fontStyleArr.length; a++){
      if(fontStyleArr[a]) SizeNum = a;
    }
    wx.getStorage({
      key: "userPreferenceBool",
      success: function(userPrefer){
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news_like&p1=0&p2=${_indexLastPort}&p3=2&p4=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}&p5=${JSON.stringify(userPrefer.data)}`,
          success: function(_P_data){
            var newPrePartArr = that.data._partArr.concat(_P_data.data.reverse());
            that.setData({_partArr: newPrePartArr, loadUp: ""});
            if(_P_data.data.length < 2){
              console.log("已到底");
              that.setData({reached: "reached", scrollUp: "scrollUp"})
            }
            that.setData({_loadmore: "_loadmore"});
            _indexLastPort = that.data._partArr[that.data._partArr.length - 1].port;
            console.log(_indexLastPort)
          },
          fail: function(err){
            console.log(err)
          }
        })
      },
      fail: function(){
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news_like&p1=0&p2=${_indexLastPort}&p3=2&p4=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}&p5=${JSON.stringify(that.data._P_Boolean)}`,
          success: function(_P_data){
            var newPrePartArr = that.data._partArr.concat(_P_data.data.reverse());
            that.setData({_partArr: newPrePartArr, loadUp: ""});
            if(_P_data.data.length < 2){
              console.log("已到底");
              that.setData({reached: "reached", scrollUp: "scrollUp"})
            }
            that.setData({_loadmore: "_loadmore"});
            _indexLastPort = that.data._partArr[that.data._partArr.length - 1].port;
            console.log(_indexLastPort)
          },
          fail: function(err){
            console.log(err)
          }
        })
      }
    })
  },
  void: function(){},
  refresh: function(e){
    that.onLoad();
  },
  _refresh: function(){
    var _fontStyleArr = wx.getStorageSync("FontSizeBool");
    var SizeNum = 0;
    for(var a=0; a<_fontStyleArr.length; a++){
      if(_fontStyleArr[a]) SizeNum = a;
    }
    wx.getStorage({
      key: "userPreferenceBool",
      success: function(userPrefer){
        console.log(userPrefer);
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news_like&p1=1&p2=0&p3=6&p4=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}&p5=${JSON.stringify(userPrefer.data)}`,
          success: function(_P_data){
            that.setData({_partArr: _P_data.data.reverse()});
            commonSettings.endowFontStyle(that, "part", "_PARTTITLEFONT");
            that.setData({_completeBoolean: false});
            _indexLastPort = _P_data.data[_P_data.data.length - 1].port;
            console.log(_indexLastPort)
          }
        })
      },
      fail: function(){
        console.log("未选择");
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news_like&p1=1&p2=0&p3=6&p4=${commonSettings.commonSettings.fontSize[SizeNum].part.limit}&p5=${JSON.stringify(that.data._P_Boolean)}`,
          success: function(_P_data){
            console.log(_P_data.data);
            that.setData({_partArr: _P_data.data.reverse()});
            commonSettings.endowFontStyle(that, "part", "_PARTTITLEFONT");
            that.setData({_completeBoolean: false});
            _indexLastPort = _P_data.data[_P_data.data.length - 1].port;
            console.log(_indexLastPort)
          },
          fail: function(err){
            console.log(err)
          }
        })
      },
    })
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
  _scroll: function(e){
    var tempTop = e.detail.scrollTop;
    _indexScrollTop = tempTop;
    if(tempTop < 10){
      that.setData({_reachedTop: true});
    }else{
      that.setData({_reachedTop: false})
    }
    if(tempTop < 200){
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
  _indexTE: function(e){
    end_Y = e.changedTouches[0].pageY;
    var shift = end_Y - start_Y
    if(that.data._reachedTop){
      if(shift < 0){
        console.log("上滑")
      }else if(shift > 10){
        console.log("下滑")
        that.setData({refresh: "refresh"});
        refreshTimer = setTimeout(function(){
          that.setData({refresh: ""});
          clearTimeout(refreshTimer)
        }, 1000);
        that._refresh();
      }
    }else{
      if(shift > 0){
        that.setData({scrollUp: "scrollUp", reached: "", _loadmore: "_loadmore"})
      }else if(shift < 0){
        that.setData({scrollUp: ""})
      }
    }
  },
  scrollToTop: function(){
    console.log(indexScrollTop);
    var temCurrt = that.data.currentNum;
    if(temCurrt == 0){
      that.setData({IndexTop: 0});
    }else if(temCurrt == 1){
      that.setData({_IndexTop: 0});
    }
    that.setData({scrollUp: ""})
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
  // ----  ----
  clearHistory: function(){
    that.setData({OPTIONNAME: that.data.IndexWindowOptions.CleanHistory, _show: "_show"})
  },
  _OK_cleanHistory: function(){
    that._closeWindow();
    wx.removeStorageSync("historyPartContent");
    wx.removeStorageSync("history");
  },
  _CANCEL_cleanHistory: function(){
    that._closeWindow()
  },
  // ----  ----
  _closeWindow: function(){
    that.setData({_show: ""})
  },
  // ----  ----
  enterWeekly: function(){
    wx.getStorage({
      key: "userInfo",
      success: function(){
        wx.navigateTo({
          url: "../weekly/weekly"
        })
      },
      fail: function(){
        that.setData({OPTIONNAME: that.data.IndexWindowOptions.enterWeekly, _show: "_show"})
      }
    })
  },
  enterFontSet: function(){
    wx.navigateTo({
      url: "../fontSet/fontSet"
    })
  },
  _OK_enterWeekly: function(){
    that._closeWindow();
  },
  _CANCEL_enterWeekly: function(){
    that._closeWindow()
  },
  userInfohandler: function(e){
    if(e.detail.userInfo){
      wx.setStorageSync("userInfo", e.detail.userInfo);
      console.log("授权成功");
      wx.login({
        success: function(getCode){
          console.log(getCode.code);
          wx.request({
            url: `https://yixinping.top/government/api/index?c=Wechat_login&m=login&p=${getCode.code}`,
            method: "POST",
            header: {
              'content-type': 'application/x-www-form-urlencoded'
            },
            data: {avatar: e.detail.userInfo.avatarUrl},
            success: function(msg){
              delete msg.data["isok"];
              wx.setStorageSync("userIds", msg.data);
              var userIds = wx.getStorageSync("userIds");
              var userPreferenceBool = JSON.stringify(wx.getStorageSync("userPreferenceBool"));
              wx.request({
                url: `https://yixinping.top/government/api/index?c=Wechat_login&m=chang_hobby&p1=${userIds.id}&p2=${userIds.openid}&p3=${userPreferenceBool}`,
                success: function(res){
                  console.log(res);
                  wx.navigateTo({
                    url: "../weekly/weekly"
                  });
                }
              })
            }
          })

        }
      });
    }else{
      console.log("授权拒绝")
    }
  }
});
