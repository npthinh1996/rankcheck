from scrapy import Spider, Request

class Proxy(Spider):
    name = "proxyTest"

    def start_requests(self):
        url = 'https://whatismyipaddress.com'
        yield Request(url=url, callback=self.parse)

    def parse(self, response):
        ip = response.xpath("//div[@id='ipv4']/a/text()").extract_first()
        print("Your ip {}".format(ip))
        return
