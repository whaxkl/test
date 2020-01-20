const puppeteer = require('puppeteer');
var fs = require('fs');
var path = require('path');
var readLine = require("readline");
//var sd = require('silly-datetime');
//var time=sd.format(new Date(), 'YYYY-MM-DD HH:mm');
//var now_date = new Date().Format("yyyy-MM-dd");//sd.format(new Date(), 'YYYY-MM-DD');
var moment = require('moment');
moment.locale('zh-cn');
var _today = moment();
var now_date = _today.format('YYYY-MM-DD'); /*现在的时间*/
var this_date = _today.format('YYYYMMDD');
var date1=new Date();
var times = date1.getTime();

(async () => {
    const browser = await puppeteer.launch({headless: false});
    const page = await browser.newPage();
    await page.goto('https://zh.semrush.com/login/?src=header');
    // await page.type('[name="email"]', '2768531174@qq.com', {delay: 100});
    // await page.type('[name="password"]', 'whaxkl', {delay: 100});
    await page.type('[name="email"]', 'gaotiansong@163.com', {delay: 350});
    await page.type('[name="password"]', '123147gts', {delay: 350});
    page.click('[data-ga-action="button.click"]');
    await page.waitFor(3000);
    // const targetLink = await page.evaluate(() => {
    //     return [...document.querySelectorAll('.result a')].filter(item => {
    //         return item.innerText && item.innerText.includes('Puppeteer的入门和实践')
    //     }).toString();
    // });
    //var sousuo=await browser.newPage();
    fs.writeFile(now_date+'-results.txt', 'count,countries,keywords,prev,current,poor,searches,traffic,domain\n',  function(err) {
        if (err) {
            return console.error(err);
        }
        console.log("数据写入成功！");
     });
    var array = fs.readFileSync('domain.txt', 'utf-8').toString().split("\n");
    for(o in array) {
        // await page.goto('https://zh.semrush.com/analytics/organic/overview/?date=20200110');
        // await page.type('[class="sc-1_3_4-input__control"]', array[i], {delay: 150});
        // page.click('.sc-1_3_4-btn__inner');
        await page.waitFor(3000);
        //page.click('.ui-flag-icon_flag_za');
        //page.click('[class="ui-flag-icon ui-flag-icon_flag_za"]');
        page.waitForNavigation();
        var str = array[o].replace('\n','');
        //await page.goto('https://zh.semrush.com/analytics/organic/overview/?db=za&searchType=domain&q='+array[i]);
        await page.goto('https://zh.semrush.com/analytics/organic/overview/?searchType=domain&q='+str+'&date='+this_date);
        //await page.reload();
        await page.waitFor(5000);
        // var keywords = await sousuo.$eval('.sc-1_3_4-link__content',el => el.innerHTML);
        // var ranking_prev = await sousuo.$eval('.cl-display-change__prev',el => el.innerHTML);
        // var keywords_current = await sousuo.$eval('.cl-display-change__current',el => el.innerHTML);
        // var a_poor = await sousuo.$eval('.cl-display-diff_sign_unchanged span',el => el.innerHTML);
        // var searches = await sousuo.$eval('.cl-positions-table-overview__volume span',el => el.innerHTML);
        // var traffic_symbol = await sousuo.$eval('.cl-positions-table-overview__trafficPercent span',el => el.innerHTML);
        // var traffic = await sousuo.$eval('.cl-positions-table-overview__trafficPercent span span',el => el.innerHTML);
        //var keywords = await page.$eval('[class="cl-summary__value"] span',el => el.innerHTML);
        //page.waitForNavigation();
        page.click('.cl-databases-top__trigger');
        await page.waitFor(5000);
        //page.waitForNavigation();
        var count = await page.$eval('[class="sc-1_3_4-pill-button__text"] span',el => el.innerHTML);
        var countries_val = await page.$$eval('.sc-1_3_4-option-ex-additional span',countriesaa => {
            return countriesaa.map(countriesaa => countriesaa.innerHTML);
        });
        var countries = await page.$$eval('.sc-1_3_4-option-ex-text',countriesbb => {
            return countriesbb.map(countriesbb => countriesbb.innerHTML);
        });
        var temp = await page.$$eval('.sc-1_3_4-option-ex-additional div:nth-child(1)',tempaa => {
            return tempaa.map(tempaa => tempaa.getAttribute('class'));
        });
        console.log(temp);
        var biaoji=0;//ui-flag-icon  
        var en_countries='us';
        var count='';
        for(let i = 0; i < countries_val.length; i++){
            if(countries_val[i]>0){
                en_countries=temp[i].split('_');
                en_countries=en_countries.pop();
                page.waitForNavigation();
                await page.goto('https://zh.semrush.com/analytics/organic/overview/?db='+en_countries+'&searchType=domain&q='+str);
                await page.waitFor(5000);
                var keywords = await page.$$eval('.cl-positions-table-overview__phrase_isRow span a span',anchors => {
                    return anchors.map(anchor => anchor.innerHTML);
                });
                var ranking_prev = await page.$$eval('.cl-display-change__prev',anchors => {
                    return anchors.map(anchor => anchor.innerHTML);
                });
                var keywords_current = await page.$$eval('.cl-display-change__current',anchors => {
                    return anchors.map(anchor => anchor.innerHTML);
                });
                var a_poor = await page.$$eval('.cl-positions-table-overview__positionDiff_isRow div div div span',anchors => {
                    return anchors.map(anchor => anchor.innerHTML);
                });
                var searches = await page.$$eval('.cl-positions-table-overview__volume_isRow span',anchors => {
                    return anchors.map(anchor => anchor.innerHTML);
                });
                var traffic_symbol = await page.$$eval('.cl-positions-table-overview__trafficPercent_isRow span',anchors => {
                    return anchors.map(anchor => anchor.innerText);
                });
                // var traffic = await page.$$eval('.cl-positions-table-overview__trafficPercent_isRow span span',anchors => {
                //     return anchors.map(anchor => anchor.innerHTML);
                // });
                count=countries_val[i];
                for(let b=0; b<keywords.length;b++) {
                    var prev='';
                    if(ranking_prev[b].length>9){
                        prev='';
                    }else{
                        prev=ranking_prev[b];
                    }
                    var search=searches[b].replace(',','');
                    fs.appendFile(now_date+'-results.txt',count+','+countries[i]+en_countries+','+keywords[b]+','+prev+','+keywords_current[b]+','+a_poor[b]+','+search+','+traffic_symbol[b]+','+array[o]+'\n',function (err) {
                        if (err){
                            console.log('The "data to append" was appended to file!');
                            return false;
                        }
                    });
                }
                biaoji = 1;
            }
        }
        if(biaoji==0){
            fs.appendFile(now_date+'-results.txt','0,,,,,,,,'+array[o]+'\n',function (err) {
                if (err){
                    console.log('The "data to append" was appended to file!');
                    return false;
                }
            });
        }
        console.log(countries.length,countries_val.length,temp.length);
        // var str = array[i].replace('\n','');
        // count = count.replace(/<[^>]+>/g,"");
        // console.log(count);
        // if(count==0){
        //     fs.appendFile(now_date+'-results.txt',count+','+str,function (err) {
        //         if (err){
        //             console.log('The "data to append" was appended to file!');
        //             return false;
        //         }
        //     });
        // }else{
        //     var keywords = await page.$$eval('.cl-positions-table-overview__phrase_isRow span a span',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     var ranking_prev = await page.$$eval('.cl-display-change__prev',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     var keywords_current = await page.$$eval('.cl-display-change__current',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     var a_poor = await page.$$eval('.cl-positions-table-overview__positionDiff_isRow div div div span',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     var searches = await page.$$eval('.cl-positions-table-overview__volume_isRow span',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     var traffic_symbol = await page.$$eval('.cl-positions-table-overview__trafficPercent_isRow span',anchors => {
        //         return anchors.map(anchor => anchor.innerText);
        //     });
        //     var traffic = await page.$$eval('.cl-positions-table-overview__trafficPercent_isRow span span',anchors => {
        //         return anchors.map(anchor => anchor.innerHTML);
        //     });
        //     for(let i=0; i<keywords.length;i++) {
        //         fs.appendFile(now_date+'-results.txt',count+','+keywords[b]+','+ranking_prev[b]+','+keywords_current[b]+','+a_poor[b]+','+searches[b]+','+traffic_symbol[b]+','+traffic[b]+','+str,function (err) {
        //             if (err){
        //                 console.log('The "data to append" was appended to file!');
        //                 return false;
        //             }
        //         });
        //     }
        
        //     console.log(keywords);
        //     console.log(ranking_prev);
        //     console.log(keywords_current);
        //     console.log(a_poor);
        //     console.log(searches);
        //     console.log(traffic_symbol);
        //     console.log(traffic);
        //     return false;
        //     fs.appendFile(now_date+'-results.txt',count+','+str,function (err) {
        //         if (err){
        //             console.log('The "data to append" was appended to file!');
        //             return false;
        //         }
        //     });
        //}
        
        //await page.type('[class="sc-1_3_4-input__control"]', keywords+","+ranking_prev+","+keywords_current+","+a_poor+","+searches+","+traffic_symbol+","+traffic, {delay: 150});
        //break;
        //return;
    }
var date2=new Date();
var times_two = date2.getTime();
console.log(times_two);
function SecondToDate(msd) {
    var time =msd
    if (null != time && "" != time) {
        if (time > 60 && time < 60 * 60) {
            time = parseInt(time / 60.0) + "分钟" + parseInt((parseFloat(time / 60.0) -
                parseInt(time / 60.0)) * 60) + "秒";
        }
        else if (time >= 60 * 60 && time < 60 * 60 * 24) {
            time = parseInt(time / 3600.0) + "小时" + parseInt((parseFloat(time / 3600.0) -
                parseInt(time / 3600.0)) * 60) + "分钟" +
                parseInt((parseFloat((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60) -
                parseInt((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60)) * 60) + "秒";
        } else if (time >= 60 * 60 * 24) {
            time = parseInt(time / 3600.0/24) + "天" +parseInt((parseFloat(time / 3600.0/24)-
                parseInt(time / 3600.0/24))*24) + "小时" + parseInt((parseFloat(time / 3600.0) -
                parseInt(time / 3600.0)) * 60) + "分钟" +
                parseInt((parseFloat((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60) -
                parseInt((parseFloat(time / 3600.0) - parseInt(time / 3600.0)) * 60)) * 60) + "秒";
        }
        else {
            time = parseInt(time) + "秒";
        }
    }
    return time;
}
console.log(SecondToDate((times_two-times)/60));

    //await page.waitFor(1000);
  //browser.close();
})()
