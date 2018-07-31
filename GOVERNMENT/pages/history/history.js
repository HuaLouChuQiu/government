// pages/history/history.js
var that;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    completeBoolean: true,
    stretch: ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    that = this;
    that.setData({historyPartArr: wx.getStorageSync("historyPartContent")});
    console.log(wx.getStorageSync("historyPartContent"))
    if(that.data.historyPartArr.length>9) that.setData({stretch: "stretch"})
    for(var a=0; a<that.data.historyPartArr.length; a++){
      var key = `historyPartArr[${a}].shortContent`
      that.setData({[key]: `${that.data.historyPartArr[a].shortContent.substr(0, 32)}...`})
    }
    for(var b=0; b<that.data.historyPartArr.length; b++){
      var key = `historyPartArr[${b}].title`
      if(that.data.historyPartArr[b].title.length>24){
        that.setData({[key]: `${that.data.historyPartArr[b].title.substr(0, 24)}...`})
      }
    }
    that.setData({completeBoolean: false});
  },
  backPage: function(){
    wx.navigateBack({
      delta: 1
    })
  },
  LoadPassage: function(e){
    console.log(e.currentTarget.dataset.port);
    wx.navigateTo({
      url: "../passages/passages?" + `port=${e.currentTarget.dataset.port}`
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