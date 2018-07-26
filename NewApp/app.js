//app.js
var that;
App({
  onLaunch: function (options) {
    that = this;
    wx.getStorage({
      key: "userInfo",
      success: function(e){
        that.globalData.userInfo = e.data;
      },
      fail: function(){
        wx.getUserInfo({
          success: function(Info){
            wx.setStorageSync("userInfo", Info.userInfo)
            that.globalData.userInfo = Info.userInfo;
          },
          fail: function(){
            wx.setStorageSync("getUserInfoFailed", true)
          }
        })
      }
    });
    wx.getNetworkType({
      success: function(e){
        if(e.networkType == "none"){
          that.globalData.hasNet = false;
        }else{
          that.globalData.hasNet = true;
        }
      }
    })
  },
  globalData: {
    userInfo: {},
    hasNet: false
  },
})