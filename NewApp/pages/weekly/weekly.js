// pages/weekly/weekly.js
var that;
var _current_ = 1;
var page_S_Y;
var page_E_Y;
var ID = "";
var OPENID = "";
var currentPort = 0;
var test = [];
console.log(test[0]);
Page({

  /**
   * 页面的初始数据
   */
  data: {
    ArrInview: ["", "Inview", "", ""],
    circularBoolean: true,
    reachTop: true,
    reachBottom: true,
    reachedTop: false,
    weeklyCurrent: 1,
    weeklyData: []
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    that = this;
    var userIds = wx.getStorageSync("userIds");
    ID = userIds.id;
    OPENID = userIds.openid;
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getlike_sum&p1=${ID}&p2=${OPENID}&p3=1&p4=0&p5=4`,
      success: function(resData){
        console.log(resData);
        console.log(`https://yixinping.top/government/api/index?c=index_news&m=getlike_sum&p1=${ID}&p2=${OPENID}&p3=1&p4=0&p5=4`)
        that.setData({weeklyData: resData.data});
      },
      fail: function(err){
        console.log(err)
      }
    })
  },
  turnToPassage: function(e){
    wx.navigateTo({
      url: "../passages/passages?" + `port=${e.currentTarget.dataset.port}`
    })
  },
  backPage: function(){
    wx.navigateBack({
      delta: 1
    })
  },
  swiped: function(e){
    var current = e.detail.current;
    currentPort = that.data.weeklyData[current].port;
    if(that.data.reachedTop){
      that.setData({reachTop: false})
    }
    var newInviewArr = that.data.ArrInview;
    for(var a=0; a<newInviewArr.length; a++){
      newInviewArr[a]=""
    }
    newInviewArr[current] = "Inview";
    that.setData({ArrInview: newInviewArr});
    var shift = page_E_Y - page_S_Y;
    if(page_E_Y == undefined){ // 解决第一次的 touchend 没有返回值的问题
      if(current == 0){
        console.log("↓↓↓↓");
        that.DownLoad(parseInt(currentPort), current)
      }else if(current == 2){
        console.log("↑↑↑↑")
      }
    }else{
      if(shift < 0){
        console.log("↑↑↑↑");
        that.UpLoad(parseInt(currentPort), current);
      }else if(shift > 0){
        console.log("↓↓↓↓");
        that.DownLoad(parseInt(currentPort), current);
      }
    }
  },
  UpLoad: function(currentPorts, currents){
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getlike_sum&p1=${ID}&p2=${OPENID}&p3=0&p4=${parseInt(currentPorts)}&p5=1`,
      success: function(resData){
        var pointer = currents + 1;
        if(pointer == 4) pointer = 0;
        var tempKey = `weeklyData[${pointer}]`;
        if(resData.data[0] != undefined){
          that.setData({[tempKey]: resData.data[0], reachedTop: false, circularBoolean: true, reachTop: true});
        }else{
          console.log("null, 到底了。");
          that.setData({circularBoolean: false, reachedBottom: true});
        }
      },
      fail: function(err){
        console.log(err)
      }
    });
  },
  DownLoad: function(currentPorts, currents){
    wx.request({
      url: `https://yixinping.top/government/api/index?c=index_news&m=getlike_sum&p1=${ID}&p2=${OPENID}&p3=0&p4=${parseInt(currentPorts)+1}&p5=-1`,
      success: function(resData){
        var pointer = currents - 1;
        if(pointer == -1) pointer = 3;
        var tempKey = `weeklyData[${pointer}]`;
        if(resData.data[0] != undefined){
          that.setData({[tempKey]: resData.data[0]});
        }else{
          console.log("null, 没有更新的了。");
          that.setData({circularBoolean: false, reachedTop: true});
        }
      },
      fail: function(err){
        console.log(err)
      }
    });
  },
  _w_start: function(e){
    console.log(e.touches[0].pageY)
    page_S_Y = e.touches[0].pageY;
  },
  _w_end: function(e){
    console.log(e)
    page_E_Y = e.changedTouches[0].pageY;
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