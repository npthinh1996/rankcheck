<?php
$val = array('keyword' => '', 'domain' => '', 'page' => '1', 'search' => 'vn');
$err = 0;
if(isset($_POST['submit'])){
    if(!empty($_POST['keyword'])){
        $val['keyword'] = $_POST['keyword'];
    } else{
        $err = 1;
    }
    $val['domain'] = $_POST['domain'];
    $val['page'] = $_POST['page'];
    $val['search'] = $_POST['search'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Rank Check</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container-fluid">
<h1 class="text-center">Rank Check</h1>
<hr>
<form action="index.php" method="post" class="row">
    <div class="col-sm-3 col-12">
        <label for="keyword">Từ khóa: <i class="font-weight-light">Cách nhau bằng dấu ','</i></label>
        <input type="search" name="keyword" id="keyword" class="form-control <?php echo $err ? "border border-danger" : ''; ?>" placeholder="Nhập từ khóa" value="<?php echo $val['keyword']; ?>">
    </div>
    <div class="col-sm-3 col-12">
        <label for="domain">Tên miền: <i class="font-weight-light">Cách nhau bằng dấu ','</i></label>
        <input type="search" name="domain" id="domain" class="form-control" placeholder="Nhập tên miền" value="<?php echo $val['domain']; ?>">
    </div>
    <div class="col-sm-2 col-12">
        <label for="page">Số trang:</label>
        <input type="number" name="page" id="page" class="form-control" min="1" max="10" value="<?php echo $val['page']; ?>">
    </div>
    <div class="col-sm-2 col-12">
        <label for="search">Khu vực:</label>
        <select name="search" id="search" class="custom-select">
            <option value="vn" <?php echo ($val['search'] == 'vn') ? 'selected' : ''; ?>>Việt Nam</option>
            <option value="jp" <?php echo ($val['search'] == 'jp') ? 'selected' : ''; ?>>Nhật Bản</option>
            <option value="kr" <?php echo ($val['search'] == 'kr') ? 'selected' : ''; ?>>Hàn Quốc</option>
        </select>
    </div>
    <div class="col-sm-2 col-12 mt-2 d-flex justify-content-end align-items-end">
        <input type="submit" name="submit" value="Tìm kiếm" class="btn text-white bg-danger">
    </div>
</form>
<hr>
<div class="text-center text-danger"><?php echo $err ? "Bạn chưa nhập vào Từ khóa!" : ''; ?></div>
</div>
<div class="container-fluid">
<?php

// [Chuyển đổi keyword từ string sang array]
if(!empty($val['keyword'])){
    $keywords = str_replace(', ', ',', $val['keyword']);
    $keywords = explode(",", $keywords);
    // [Kiểm tra domain và chuyển từ string sang array]
    if(!empty($val['domain'])){
        $domains = str_replace(', ', ',', $val['domain']);
        $domains = explode(",", $domains);
    }
    
    // [Tạo file tạm để lưu kết quả với tên ngẫu nhiên]
    $name_f = md5(rand()) . ".jl";

    foreach($keywords as $key){
        // [Chọn lấy kết quả từ bing.com hoặc google.com]
        $cmd = "scrapy crawl getRankBPHP -o $name_f ";
        // $cmd = "scrapy crawl getRankGPHP -o $name_f ";
        $arg = " -a keyword=" . urlencode($key) . " -a page=" . $val['page'] . " -a search=" . $val['search'];
        system($cmd . $arg);
    }
?>
    <table class="table table-striped">
        <thead>
            <tr class="text-light">
                <th scope="col" class="sticky-top bg-success">Từ khóa</th>
                <th scope="col" class="sticky-top bg-success">Link</th>
                <th scope="col" class="sticky-top bg-success">Tên miền</th>
                <th scope="col" class="sticky-top bg-success">Vị trí</th>
                <th scope="col" class="sticky-top bg-success">Trang</th>
                <th scope="col" class="sticky-top bg-success">Hạng</th>
                <th scope="col" class="sticky-top bg-success">Khu vực</th>
            </tr>
        </thead>
        <tbody>
<?php
    // [Kiểm tra và đọc dữ liệu từ file tạm]
    $file = file_exists($name_f) ? file_get_contents($name_f) : "";
    // [Chuẩn hóa từ *.jl sang *.json]
    $file = '[' . preg_replace("/}\s./", "}, {", $file) . ']';
    $file = json_decode($file);
    
    // [Xóa file tạm]
    system("del " . $name_f);
    // system("rm " . $name_f);

    if(isset($file)){
        foreach($file as $item){
            if(empty($domains) || in_array($item->domain, $domains)){
                echo "<tr>";
                foreach($item as $key => $val){
                    if($key == "url") $val = "<a href='" . $val . "' target='_blank'>" . $val . "</a>";
                    echo "<td>" . $val . "</td>";
                }
                echo "</tr>";
            }
        }
    }
}
?>
        </tbody>
    </table>
</div>
</body>
</html>
