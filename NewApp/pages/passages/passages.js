// pages/passages/passages.js
var that;
var clipboardTimer;
Page({
  data: {
    okImg: "none",
    okSrc: "",
    okEvent: "copy_to_clipboard",
    okColor: "#898989",
    okTip: "复制原文地址链接",
    passageJSON: {
      info: {title: "视觉风格仍在调整，多任务切换操作进一步优化：Android P DP4 更新 | 具透", site: "中央政府网", time: "7月12日", author: "新华社记者", url: "http://www.gov.cn"}, 
      keyWords: ["A", "AB", "ABC", "ABCD", "ABCDE", "ABCDEF", "ABCDEFG", "ABCDEFGH", "ABCDEFGHI", "ABCDEFGHIJ", "ABCDEFGHIJK"],
      content: [
        {image: "", text: "现在的互联网上「贩卖焦虑」似乎成了永不缺乏的「十万加」话题。抛开这些文章的观点是否偏激不谈，「贩卖焦虑」的流行至少说明了一个事实：我们当中的很多人，或多或少都会在生活中感到焦虑。"},
        {image: "", text: "焦虑让人睡眠质量下降，注意力无法长时间集中，工作效率也会受其影响，整个人陷入身心俱疲的恶性循环。因此，如何自我调节、舒缓压力就成了大家关心的话题。"},
        {image: "http://www.gov.cn/govweb/c1293/201807/5306963/images/022b98b3cecb4785a767e1608199f821.jpg", text: ""},
        {image: "", text: "此前我们曾经向大家推荐过番茄钟应用潮汐，不同于传统的番茄钟应用，它利用「白噪音使人内心平静、精神专注」的特点，将白噪音和番茄钟结合，有效地避免了「定个番茄钟然后跑神25分钟」的情况。"},
        {image: "", text: "睡眠的重要性无需强调，你在每一个失眠的夜晚和被闹钟「吓醒又昏倒」的清晨应该都能深切体会到。人的一生有近三分之一时间要在睡眠中度过，与其天天祈祷「睡到自然醒」，不如主动尝试改善自己的睡眠质量。"},
        {image: "", text: "当窗外下雨时，你在屋内就能睡得特别香。这样一种均匀而持续的声音让你忽略环境中突然闯入的鸣笛、脚步等声音，又常常被称为白噪音。潮汐的助眠功能采取的就是这样一种方式，播放白噪音，通过掩蔽睡眠环境下的杂音来达到一个声音波动的相对平衡，帮助我们的大脑在睡眠时减少被外界声音干扰的可能性。"},
        {image: "", text: "不同于系统自带的「吓人」闹钟声，潮汐采用了友好的自然声音唤你起床，挪威森林的鸟鸣、曼彻斯特海边的海浪、东京寺庙的钟声与鸡鸣，相信这些有趣的声音相信一定可以让你每天的醒来都感到愉悦。"}
      ]
    }
  },
  backPage: function(){
    wx.navigateBack({
      delta: 1
    })
  },
  onLoad: function (options) {
    that = this;
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