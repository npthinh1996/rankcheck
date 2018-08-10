# -*- coding: utf-8 -*-

# Define your item pipelines here
#
# Don't forget to add your pipeline to the ITEM_PIPELINES setting
# See: https://doc.scrapy.org/en/latest/topics/item-pipeline.html

# [Đổi tên thành pipelines.py sau khi config]

from pymysql import connect

class GetrankPipeline(object):
    def process_item(self, item, spider):
        self.conn = connect(host='...', user='...', password='...', db='scraper')
        self.cursor = self.conn.cursor()
        self.cursor.execute("INSERT INTO rankcrawl (keyword, url, domain, rank, date) VALUES ('{}', '{}', '{}', {}, '{}')".format(item['keyword'], item['url'], item['domain'], item['rank'], item['date']))
        self.conn.commit()

        self.conn.close()
        return item
