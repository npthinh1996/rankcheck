# Yêu cầu
* Python >= 3.*
* Scrapy
* Pymysql
# Lưu ý
* Tùy chỉnh lại proxy tại ./rankCheck/middlewares.py
* Tạo CSDL scraper gồm 2 bảng keywords và rankcheck
    * keywords: id, keyword
    * rankcheck: keyId, url, domain, position, page, rank, date, searchId
