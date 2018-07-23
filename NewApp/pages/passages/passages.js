// pages/passages/passages.js
var that;
var clipboardTimer;
var start_Y=0;
var end_Y=0;
var timer;
var ThePort = 0;
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
        console.log("run", passageData.data);
        that.setData({passageJSON: passageData.data});
      },
      complete: function(){
        that.setData({completeBoolean: false})
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
    that.getpassage();
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
  indexTS: function(e){
    start_Y = e.touches[0].pageY
  },
  indexTE: function(e){
    end_Y = e.changedTouches[0].pageY;
    var shift = end_Y - start_Y
    console.log(shift);
    if(shift<0){
      timer = setTimeout(function(){
        that.setData({hideBar: "hideBar"});
        clearTimeout(timer);
      }, 1000)
    }else if(shift>0){
      that.setData({hideBar: ""})
    }
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