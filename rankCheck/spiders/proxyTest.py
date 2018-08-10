from scrapy import Spider, Request

class Proxy(Spider):
    name = "proxyTest"

    def start_requests(self):
        url = 'https://whatismyipaddress.com'
        yield Request(url=url, callback=self.parse)

    def parse(self, response):
        ip4 = response.xpath("//div[@id='ipv4']//a/text()").extract_first()
        ip6 = response.xpath("//div[@id='ipv6']//a/text()").extract_first()
        reg = response.xpath("//th[text() = 'Region:']/../td/text()").extract_first()
        print("IP4: {}".format(ip4))
        print("IP6: {}".format(ip6))
        print("Region: {}".format(reg))
        return
