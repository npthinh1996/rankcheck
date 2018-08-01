from re import sub
from scrapy import Spider, Request
from urllib.parse import unquote_plus
from ..items import GetrankkItem

class Scraper(Spider):
    name = 'getRankGPHP'

    # [Nhận giá trị truyền vào]
    def __init__(self, keyword=None, page=None, search=None, *args, **kwargs):
        super(Scraper, self).__init__(*args, **kwargs)
        self.keyword = keyword
        self.page = int(page)
        self.search = search

    def start_requests(self):
        idxR = 0
        url = 'https://www.google.com/search?q={}&gl={}'.format(self.keyword, self.search)
        yield Request(url=url, callback=self.parse, meta={'idxR':idxR})

    def parse(self, response):
        rankItem = GetrankkItem()
        
        idx = 0
        idxR = response.meta['idxR']
        for item in response.css('div.rc'):
            idx += 1
            idxR += 1

            rankItem['keyword'] = unquote_plus(self.keyword)
            rankItem['url'] = item.css('h3.r a::attr(href)').extract_first()
            rankItem['domain'] = sub(r'(http.?://)|(www.)|(/.*)', '', rankItem['url'])
            rankItem['position'] = idx
            rankItem['page'] = int(response.css('td.cur::text').extract_first())
            rankItem['rank'] = idxR
            rankItem['search'] = self.search

            yield rankItem

        # [Lấy kết quả ở trang tiếp theo]
        next_page = response.css('a#pnnext::attr(href)').extract_first()
        if next_page is not None:
            page = rankItem['page']
            if page < self.page:
                yield response.follow(url=next_page, callback=self.parse, meta={'idxR':idxR})
