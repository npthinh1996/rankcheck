# -*- coding: utf-8 -*-

# Define here the models for your scraped items
#
# See documentation in:
# https://doc.scrapy.org/en/latest/topics/items.html

from scrapy import Item, Field

class GetrankItem(Item):
    keyword = Field()
    url = Field()
    title = Field()
    domain = Field()
    rank = Field()
    date = Field()
