var commonSettings = {
    fontSize: [
        {// large
            part: {
                style: "900 34rpx/40rpx",
                limit: 24
            },
            passagesTitle: "800 44rpx/48rpx",
            passagesOrigin: "400 26rpx/40rpx",
            passagesP: "400 34rpx/52rpx"
        },
        {// medium
            part: {
                style: "900 30rpx/40rpx",
                limit: 28
            },
            passagesTitle: "800 38rpx/44rpx",
            passagesOrigin: "400 23rpx/35rpx",
            passagesP: "400 30rpx/49rpx"
        },
        {// small
            part: {
                style: "900 27rpx/40rpx",
                limit: 24
            },
            passagesTitle: "800 32rpx/40rpx",
            passagesOrigin: "400 20rpx/30rpx",
            passagesP: "400 25rpx/45rpx"
        }
    ]
}
function endowFontStyle(those, key, nameData, userDefined=-1){
    var fontSizeArr = wx.getStorageSync("FontSizeBool");
    var SizeNum = 0;
    if(userDefined == -1){
        for(var a=0; a<fontSizeArr.length; a++){
          if(fontSizeArr[a]) SizeNum = a;
        }
    }else{
        SizeNum = userDefined;
    }
    var fontStyle = "";
    if(key == "passagesTitle") fontStyle = commonSettings.fontSize[SizeNum].passagesTitle;
    if(key == "passagesOrigin") fontStyle = commonSettings.fontSize[SizeNum].passagesOrigin;
    if(key == "passagesP") fontStyle = commonSettings.fontSize[SizeNum].passagesP;
    if(key == "part") fontStyle = commonSettings.fontSize[SizeNum].part.style;
    console.log(fontStyle)
    those.setData({[nameData]: fontStyle});
}
module.exports.commonSettings = commonSettings;
module.exports.endowFontStyle = endowFontStyle;