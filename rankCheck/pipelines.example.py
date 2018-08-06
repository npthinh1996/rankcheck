# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: https://doc.scrapy.org/en/latest/topics/item-pipeline.html

# [Đổi tên thành pipelines.py sau khi config]

from pymysql import connect

class RankcheckPipeline(object):
    def process_item(self, item, spider):
        self.conn = connect(host='...', user='...', password='...', db='scraper')
        self.cursor = self.conn.cursor()
        self.cursor.execute("INSERT INTO rankcheck (keyId, url, domain, position, page, rank, date, searchId) VALUES ({}, '{}', '{}', '{}', {}, {}, '{}', {})".format(item['keyId'], item['url'], item['domain'], item['position'], item['page'], item['rank'], item['date'], item['searchId']))
        self.conn.commit()

        self.conn.close()
        return item

class GetrankPipeline(object):
    def process_item(self, item, spider):
        self.conn = connect(host='localhost', user='root', password='', db='scraper')
        self.cursor = self.conn.cursor()
        self.cursor.execute("INSERT INTO rankcrawl (keyword, url, domain, position, page, rank, search) VALUES ('{}', '{}', '{}', {}, {}, {}, '{}')".format(item['keyword'], item['url'], item['domain'], item['position'], item['page'], item['rank'], item['search']))
        self.conn.commit()

        self.conn.close()
        return item
