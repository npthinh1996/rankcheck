# [Đổi tên thành rankBCheck.py sau khi config]

from re import sub
from datetime import date
from pymysql import connect
from scrapy import Spider, Request
from urllib.parse import quote_plus
from ..items import RankcheckItem

class Scraper(Spider):
    name = '...'
    # name = 'rankBCheck'
    
    def start_requests(self):
        conn = connect(host='...', user='...', password='...', db='scraper')
        cursor = conn.cursor()
        cursor.execute("SELECT * FROM keywords")
        cont = cursor.fetchall()
        conn.close()

        for i in cont:
            idxR = 0
            url = 'https://www.bing.com/search?q={0}'.format(quote_plus(i[1]))
            yield Request(url=url, callback=self.parse, meta={'keyId':i[0], 'idxR':idxR})

    def parse(self, response):
        rankItem = RankcheckItem()
        
        idx = 0
        keyId = response.meta['keyId']
        idxR = response.meta['idxR']
        for item in response.css('li.b_algo'):
            idx += 1
            idxR += 1

            rankItem['keyId'] = keyId
            rankItem['url'] = item.css('h2 a::attr(href)').extract_first()
            rankItem['domain'] = sub(r'(http.?://)|(/.*)', '', rankItem['url'])
            rankItem['position'] = idx
            rankItem['page'] = int(response.css('a.sb_pagS_bp::text').extract_first())
            rankItem['rank'] = idxR
            rankItem['date'] = date.today()
            rankItem['searchId'] = 2

            yield rankItem

        next_page = response.xpath('//li[@class="b_pag"]//li[last()]/a/@href').extract_first()
        if next_page is not None:
            page = rankItem['page']
            if page < 3:
                yield response.follow(url=next_page, callback=self.parse, meta={'keyId':keyId, 'idxR':idxR})
