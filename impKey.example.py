# [Đổi tên thành impKey.py sau khi config]

from pymysql import connect

# [Kết nối database]
conn = connect(host='...', user='...', password='...', db='scraper')
cursor = conn.cursor()

# [Đọc keyword từ file key.txt và chuyển thành tuple]
with open('key.txt', encoding="utf-8-sig") as df:
    data = df.readlines()
    data = tuple((el.strip()) for el in data)

# [Cập nhật dữ liệu lên database]
for i in data:
    cursor.execute("INSERT INTO keywords (id, keyword) VALUE (null, %s)", (i))
    conn.commit()
    print(i)
    
conn.close()
