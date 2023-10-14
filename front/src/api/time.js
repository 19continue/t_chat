function formatTime2(fmt, timestamp) {
    var date = new Date(timestamp) // 兼容safari
    var o = {
        'M+': date.getMonth() + 1,
        'd+': date.getDate(),
        'h+': date.getHours(),
        'm+': date.getMinutes(),
        's+': date.getSeconds(),
        'q+': Math.floor((date.getMonth() + 3) / 3),
        'S': date.getMilliseconds()
    }
    if (/(y+)/.test(fmt)) {
        fmt = fmt.replace(RegExp.$1, (date.getFullYear() + '').substr(4 - RegExp.$1.length))
    }
    for (var k in o) {
        if (new RegExp('(' + k + ')').test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length === 1) ? (o[k]) : (('00' + o[k]).substr(('' + o[k]).length)))
        }
    }
    return fmt
}
function weekFormat(e, prefix = "周") {
    switch (e) {
        case 0:
            return prefix + "日"
            break;
        case 1:
            return prefix + "一"
            break;
        case 2:
            return prefix + "二"
            break;
        case 3:
            return prefix + "三"
            break;
        case 4:
            return prefix + "四"
            break;
        case 5:
            return prefix + "五"
            break;
        case 6:
            return prefix + "六"
            break;
        default:
            return ""
            break;
    }
}
// 转换今日的时辰格式
function todayTimeFormat(e) {
    if (e >= 0 && e < 7) {
        return "凌晨"
    } else if (e >= 7 && e < 11) {
        return "上午"
    } else if (e >= 11 && e < 13) {
        return "中午"
    } else if (e >= 13 && e < 18) {
        return "下午"
    } else if (e >= 18 && e < 24) {
        return "晚上"
    } else {
        return ""
    }
}

// 是否显示周几
function isShowWeekDay(sub, weekDay) {
    const currentWeekDay = new Date().getDay()
    const dayTime = 1000 * 60 * 60 * 24
    // 1.当前时间与消息时间相差必须大于2天小于7天
    // 2.当前时间距离本周一相差必须大于2天且小于当前距离周一的天数
    // 3.消息时间不可能是0-周日，因为周日没有给后面时间留空间，不会走这里的逻辑，而是走今天的逻辑
    if (sub >= dayTime * 2 && weekDay !== 0 && sub <= dayTime * currentWeekDay) {
        return true
    } else {
        return false
    }
}


export function timeFormat(time,carefully=false) {
    if(!time){
        return ''
    }
    time = Date.parse(new Date(time))
    const today = new Date().getTime()
    // 当前时间减去获取到的时间
    const sub = today - time
    const day = 1000 * 60 * 60 * 24
    const timeDate = new Date(time)
    const currentYear = new Date().getFullYear()
    const getYear = new Date(time).getFullYear()
    const HH = formatTime2("hh", timeDate)
    const hh = formatTime2("mm", timeDate)
    const HHmm = `${HH}:${hh}`
    const showWeekDay = isShowWeekDay(sub, timeDate.getDay())
    if (currentYear > getYear) {
        if(!carefully){
            return `${formatTime2("yyyy-MM-dd", timeDate)} ${todayTimeFormat(timeDate.getHours())} ${formatTime2("hh:mm", timeDate)}`
        }else{
            return `${formatTime2("yyyy-MM-dd", timeDate)}`
        }
    } else if (showWeekDay) {
        if(!carefully){
            return `${weekFormat(timeDate.getDay())} ${HHmm}`
        }else{
            return `${weekFormat(timeDate.getDay())}`
        }    
    } else if (sub > day && sub < day * 2) {
        return `昨天 ${HHmm}`
    } else if (sub <= day) {
        if(carefully){
            if (sub < 60000) {
                return '刚刚'
            }
            else if (sub < 3600000) {
                return `${Math.floor(sub / 60000)}分钟前`
            }
            else if(HH<6){
                return `凌晨${HHmm}`
            }
            else if (HH < 12&&HH >= 6) {
                return `上午${HHmm}`
            }
            else if (HH == 12) {
                return `中午${HHmm}`
            }
            else if (HH > 12 && HH < 18) {
                return `下午${HHmm}`
            }
            else if (HH > 18) {
                return `晚上${HHmm}`
            }
            else {
                return HHmm
            }
        }else{
            if (HH < 6) {
                return `凌晨${HHmm}`
            }
            else if (HH < 12) {
                return `上午${HHmm}`
            }
            else if (HH == 12) {
                return `中午${HHmm}`
            }
            else if (HH > 12 && HH < 18) {
                return `下午${HHmm}`
            }
            else if (HH > 18) {
                return `晚上${HHmm}`
            }
            else {
                return HHmm
            }
        }
    } else {
        if(!carefully){
            return `${formatTime2("MM-dd", timeDate)} ${todayTimeFormat(timeDate.getHours())} ${formatTime2("hh:mm", timeDate)}`
        }
        else{
            return `${formatTime2("MM-dd", timeDate)}`
        }
    }
} 