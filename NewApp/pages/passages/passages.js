// pages/passages/passages.js
const App = getApp();
var that;
var clipboardTimer;
var start_Y=0;
var end_Y=0;
var timer;
var ThePort = 0;
function cutArr(array){
  var tempArr = [];
  for(var b=0; b<array.length; b++){
    if(tempArr.indexOf(array[b]) === -1){
      tempArr.push(array[b]);
    }
  }
  return tempArr;
}
function cutJSONArr(array){
  for(var a=0; a<array.length; a++){
    for(var b=a+1; b<array.length;){
      if(array[a].port == array[b].port){
        array.splice(b, 1);
      }else{
        b++;
      }
    }
  }
  return array;
}
Page({
  data: {
    completeBoolean: true,
    okImg: "none",
    okSrc: "",
    okEvent: "copy_to_clipboard",
    okColor: "#898989",
    okTip: "复制原文地址链接",
    passageJSON: {}
  },
  getpassage: function(){
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getNews_contecnt&p1=${ThePort}`,
      success: function(passageData){
        that.setData({passageJSON: passageData.data, completeBoolean: false});
      },
      complete: function(){
      },
      fail: function(){
        that.setData({completeBoolean: true});
      }
    })
  },
  backPage: function(){
    wx.navigateBack({
      delta: 1
    })
  },
  onLoad: function (options) {
    that = this;
    ThePort = options.port;
    that.getpassage(options.net);
    wx.getStorage({
      key: "history",
      success: function(prevHistory){
        var currentHistory = [];
        currentHistory.push(options.port);
        for(var a=0; a<prevHistory.data.length; a++){
          currentHistory.push(prevHistory.data[a])
        }
        var newTempArr = cutArr(currentHistory);
        if(newTempArr.length>50){newTempArr.splice(50, 1)}
        wx.setStorageSync("history", newTempArr);
        wx.getStorage({
          key: "history",
          success: function(TempCurrHistoryData){
            wx.getStorage({
              key: "historyPartContent",
              success: function(partContentData){
                wx.request({
                  url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news&p1=${parseInt(TempCurrHistoryData.data[0])+1}&p2=1`,
                  success: function(tempData){
                    var tempPartArr = [];
                    tempPartArr.push(tempData.data[0]);
                    for(var c=0; c<partContentData.data.length; c++){
                      tempPartArr.push(partContentData.data[c])
                    }
                    var newTempPartArr = cutJSONArr(tempPartArr);
                    if(newTempPartArr.length>50){newTempPartArr.splice(50, 1)};
                    wx.setStorageSync("historyPartContent", newTempPartArr);
                  },
                  fail: function(err){
                    console.log(err)
                  }
                })
              }
            });
          },
          fail: function(err){
            console.log(err)
          }
        })
      },
      fail: function(){
        var newHistory = [];
        newHistory.push(options.port);
        wx.setStorageSync("history", newHistory);
        wx.request({
          url: `https://yixinping.top/government/api/index?c=index_news&m=getState_news&p1=${parseInt(options.port)+1}&p2=1`,
          success: function(OnePartArr){
            var newPartArr = [];
            newPartArr.push(OnePartArr.data[0]);
            wx.setStorageSync("historyPartContent", newPartArr);
          }
        })
      }
    })
  },
  copy_to_clipboard: function(){
    var originUrl = that.data.passageJSON.info.url;
    wx.setClipboardData({
      data: originUrl,
      success: function(res){
        that.setData({okImg: "block", okSrc: "../../images/ok.svg", okEvent: "", okColor: "#3bc16c", okTip: "链接复制成功！"});
        clipboardTimer = setTimeout(function(){
          that.setData({fade: "fade"});
          setTimeout(function(){
            that.setData({fade: "", okImg: "none", okSrc: "", okEvent: "copy_to_clipboard", okColor: "#898989", okTip: "复制原文地址链接"});
            clearTimeout(clipboardTimer)
          }, 400)
        }, 5000)
      }
    })
  },
  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
  
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
  
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
  
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
  
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
  
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
  
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
  
  }
})