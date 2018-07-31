// pages/fontSet/fontSet.js
var commonSettings = require("../../AppSeetings.js");
var that;
Page({

  /**
   * 页面的初始数据
   */
  data: {
    showBool: true,
    styleNameArr: [
      {
        active: "",
        data: "小"
      },
      {
        active: "",
        data: "中"
      },
      {
        active: "active",
        data: "大"
      }
    ],
    displayStyle: ""
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    that = this;
    var fontStyleArr = wx.getStorageSync("FontSizeBool");
    var SizeNum = 0;
    for(var a=0; a<fontStyleArr.length; a++){
      if(fontStyleArr[a]) SizeNum = a;
    }
    that.setExampleFontStyle(fontStyleArr.length - SizeNum - 1, that.data.styleNameArr[fontStyleArr.length - SizeNum - 1].data, fontStyleArr.length);
    that.setData({showBool: false});
  },
  backPage: function(){
    wx.navigateBack({
      delta: 1
    })
  },
  slected_change: function(e){
    var currStyleNameArr = that.data.styleNameArr;
    var styleName = e.currentTarget.dataset.stylename;
    var currID = e.currentTarget.dataset.currid;
    that.setExampleFontStyle(currID, styleName, currStyleNameArr.length);
    var newFontStyleArr = [false, false, false];
    newFontStyleArr[currStyleNameArr.length - currID - 1] = true;
    wx.setStorageSync("FontSizeBool", newFontStyleArr);
  },
  setExampleFontStyle: function(currID, styleName, length){
    that.setData({displayStyle: styleName});
    for(var a=0; a<length; a++){
      var tempKey = `styleNameArr[${a}].active`;
      that.setData({[tempKey]: ""})
    }
    var OneTempKey = `styleNameArr[${currID}].active`;
    that.setData({[OneTempKey]: "active"});
    commonSettings.endowFontStyle(that, "passagesTitle", "_title", length - currID - 1);
    commonSettings.endowFontStyle(that, "passagesOrigin", "passages_origin", length - currID - 1);
    commonSettings.endowFontStyle(that, "passagesP", "passages_P", length - currID - 1);
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