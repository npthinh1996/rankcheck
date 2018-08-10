<?php
$val = array('keyword' => '', 'domain' => '', 'page' => '1', 'search' => 'google');
$err = 0;
if(isset($_POST['submit'])){
    if(!empty($_POST['keyword'])){
        $val['keyword'] = $_POST['keyword'];
    }
    if($_FILES['file']['tmp_name']){
        $file = $_FILES['file'];
        $val['keyword'] = '';
        if(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) != "csv"){
            $err = 1;
        }
    }
    if(empty($_POST['keyword']) && !$_FILES['file']['tmp_name']){
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
    <title>Rank Check | Mango Ads</title>
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
</head>
<body>
<div class="container-fluid">
<h1 class="text-center">Rank Check</h1>
<hr>
<form action="index.php" method="post" class="row" enctype="multipart/form-data">
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
        <label for="search">Công cụ:</label>
        <select name="search" id="search" class="custom-select">
            <option value="google" <?php echo ($val['search'] == 'google') ? 'selected' : ''; ?>>Google</option>
            <option value="bing" <?php echo ($val['search'] == 'bing') ? 'selected' : ''; ?>>Bing</option>
        </select>
    </div>
    <div class="col-sm-2 col-12 mt-2 d-flex justify-content-end align-items-end">
        <input type="submit" name="submit" value="Tìm kiếm" class="btn btn-danger">
    </div>
    <div class="col-sm-3 col-12 mt-2">
        <input type="file" name="file" id="file" class="form-control-file">
    </div>
</form>
<hr>
<div class="text-center text-danger"><?php echo $err ? "Bạn chưa nhập vào 'Từ khóa' hoặc nhập File '.csv'" : ''; ?></div>
</div>
<div class="container-fluid">
<?php

// [Chuyển đổi keyword từ string sang array]
if((!empty($val['keyword']) || !empty($file)) && !$err){    
    // [Tạo file tạm để lưu kết quả với tên ngẫu nhiên]
    $name_r = md5(rand());

    $name_f = $name_r . ".jl";
    $name_c = $name_r . ".csv";

    // [Xét file upload]
    if(!empty($file)){
        move_uploaded_file($file["tmp_name"], "tmp/" . $name_c);
        $file_tmp = file_get_contents("tmp/" . $name_c);
        $file_tmp = mb_convert_encoding($file_tmp, "UTF-8", "UTF-16LE");
        $file_data = str_getcsv($file_tmp, "\n");
        // [Xóa tiêu đề của file]
        unset($file_data[0]);

        $val['group'] = array();
        $val['trend'] = array();

        foreach($file_data as $fd){
            $fd = str_getcsv($fd, "\t");
            array_push($val['group'], $fd[0]);
            $val['keyword'] .= $fd[1] . ",";
            array_push($val['trend'], $fd[2]);
        }

        $val['keyword'] = rtrim($val['keyword'], ',');
    }

    // [Chuyển keyword từ chuỗi sang mảng]
    $keywords = str_replace(', ', ',', $val['keyword']);
    $keywords = explode(",", $keywords);
    // [Kiểm tra domain và chuyển từ string sang array]
    if(!empty($val['domain'])){
        $domains = str_replace(', ', ',', $val['domain']);
        $domains = explode(",", $domains);
    }

    foreach($keywords as $key){
        $cmd = "scrapy crawl getRankPHP -o tmp/$name_f ";
        $arg = " -a keyword=" . urlencode($key) . " -a page=" . $val['page'] . " -a search=" . $val['search'];
        system($cmd . $arg);
    }
?>
    <table class="table table-striped table-bordered table-responsive">
        <tbody>
<?php
    // [Kiểm tra và đọc dữ liệu từ file tạm]
    $load = file_exists('tmp/' . $name_f) ? file_get_contents('tmp/' . $name_f) : "";
    // [Chuẩn hóa từ *.jl sang *.json]
    $load = '[' . preg_replace("/}\s./", "}, {", $load) . ']';
    $load = json_decode($load);
    
    // [Xóa file tạm]
    unlink('tmp/' . $name_f);
    if(!empty($file)){
        unlink('tmp/' . $name_c);
    }

    if(isset($load)){
        foreach($keywords as $idx => $kw){
            echo "<tr>";
            echo "<td>" . $kw . "</td>";
            if(!empty($file)){
                echo "<td>" . $val['group'][$idx] . "</td>";
                echo "<td>" . $val['trend'][$idx] . "</td>";
            }
            foreach($load as $item){
                if($item->keyword == $kw && (empty($domains) || in_array($item->domain, $domains))){
                    echo "<td><a href='" . $item->url . "'>" . $item->url . "</a></td>";
                    echo "<td>" . $item->domain . "</td>";
                    echo "<td>" . $item->rank . "</td>";
                }
            }
            echo "</tr>";
        }
    }
}
?>
        </tbody>
    </table>
</div>
</body>
</html>
