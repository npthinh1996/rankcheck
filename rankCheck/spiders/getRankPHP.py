from re import sub
from datetime import date
from scrapy import Spider, Request
from urllib.parse import unquote_plus
from ..items import GetrankItem

class Scraper(Spider):
    name = 'getRankPHP'

    # [Nhận giá trị truyền vào]
    def __init__(self, keyword=None, page=None, search=None, *args, **kwargs):
        super(Scraper, self).__init__(*args, **kwargs)
        self.keyword = keyword
        self.page = int(page)
        self.search = search

    def start_requests(self):
        rank = 0
        search = self.search
        urlS = {
            'google': 'https://www.google.com/search?q=',
            'bing': 'https://www.bing.com/search?q='
        }

        url = '{}{}'.format(urlS[search], self.keyword)
        yield Request(url=url, callback=self.parse, meta={'rank':rank})

    def parse(self, response):
        rankItem = GetrankItem()
        search = self.search

        itemS = {
            'google': {
                'wrap': 'div.rc',
                'url': 'h3.r a::attr(href)',
                'page': 'td.cur::text',
                'next': '//a[@id="pnnext"]/@href'
            },
            'bing': {
                'wrap': 'li.b_algo',
                'url': 'h2 a::attr(href)',
                'page': 'a.sb_pagS_bp::text',
                'next': '//li[@class="b_pag"]//li[last()]/a/@href'
            }
        }
        
        rank = response.meta['rank']
        for item in response.css('{}'.format(itemS[search]['wrap'])):
            rank += 1

            rankItem['keyword'] = unquote_plus(self.keyword)
            rankItem['url'] = item.css('{}'.format(itemS[search]['url'])).extract_first()
            rankItem['domain'] = sub(r'(http.?://)|(www.)|(/.*)', '', rankItem['url'])
            rankItem['rank'] = rank
            rankItem['date'] = date.today()

            yield rankItem

        # [Lấy kết quả ở trang tiếp theo]
        next_page = response.xpath('{}'.format(itemS[search]['next'])).extract_first()
        if next_page is not None:
            page = int(response.css('{}'.format(itemS[search]['page'])).extract_first())
            if page < self.page:
                yield response.follow(url=next_page, callback=self.parse, meta={'rank':rank})
