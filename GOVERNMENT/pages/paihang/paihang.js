//index.js
//获取应用实例
var Next=[];
const app = getApp()
Page({
  data: {
    motto: 'Hello World',
    userInfo: {},
    hasUserInfo: false,
    canIUse: wx.canIUse('button.open-type.getUserInfo'),
    index:{},
    next:{}
  },
  //事件处理函数
  bindViewTap: function() {
    wx.navigateTo({
      url: '../logs/logs'
    })
  },
  onLoad: function () {
    var that = this;
    if (app.globalData.userInfo) {
      this.setData({
        userInfo: app.globalData.userInfo,
        hasUserInfo: true
      })
    } else if (this.data.canIUse){
      // 由于 getUserInfo 是网络请求，可能会在 Page.onLoad 之后才返回
      // 所以此处加入 callback 以防止这种情况
      app.userInfoReadyCallback = res => {
        this.setData({
          userInfo: res.userInfo,
          hasUserInfo: true
        })
      }
    } else {
      // 在没有 open-type=getUserInfo 版本的兼容处理
      wx.getUserInfo({
        success: res => {
          app.globalData.userInfo = res.userInfo
          this.setData({
            userInfo: res.userInfo,
            hasUserInfo: true
          })
        }
      })
    }
    wx.request({
      url: 'https://find.sssnow.cn/php/guangxun/top.php',
      header: {
        'content-type': 'application/json',
      },
      method: 'GET',
      success: function(res) {
        that.setData({
          'index': res.data
        })
        console.log(that.data.index.length)
        for (var i = 3; i < that.data.index.length; i++) {
          Next.push(that.data.index[i])
        }
        that.setData({
          'next': Next
        })
        console.log(Next)
        console.log(that.data.next.length)
      },
      fail: function(res) {
        console.log(res)
      }, 
      complete: function(res) {
        console.log(res)
      },
    })
    
  },
  turnToMain: function(e){
    console.log(e.currentTarget.dataset.id)
    wx.navigateTo({
      url: `../main/main?id=${e.currentTarget.dataset.id}`
    })
  },
  getUserInfo: function(e) {
    console.log(e)
    app.globalData.userInfo = e.detail.userInfo
    this.setData({
      userInfo: e.detail.userInfo,
      hasUserInfo: true
    })
  }
})
