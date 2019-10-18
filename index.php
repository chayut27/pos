<?php
session_start();
if(!isset($_SESSION["account_info"])){
    header("location:login.php");
}
// session_destroy();

$con = new mysqli("localhost", "root", "" , "system_pos");
$con->set_charset("utf-8");


if(isset($_GET["act"]) && $_GET["act"] == "add" && isset($_GET["product_id"])){

    if(!isset( $_SESSION["intLine"])){
        $_SESSION["intLine"] = 0;
        $_SESSION["product_id"][0] = $_GET["product_id"];
        $_SESSION["qty"][0] = 0;
    }

        $key = array_search($_GET["product_id"],  $_SESSION["product_id"]);
        if((string)$key != ""){
            $_SESSION["qty"][$key] = $_SESSION["qty"][$key] +1;
        }else{
            $_SESSION["intLine"] = $_SESSION["intLine"] + 1;
            $newLine = $_SESSION["intLine"];
            $_SESSION["product_id"][$newLine] = $_GET["product_id"];
            $_SESSION["qty"][$newLine] = 1;
        }

    header('location:./');
        
    // echo "<pre>";
    // print_r($_SESSION);
    // echo "</pre>";
   
}elseif(isset($_GET["act"]) && $_GET["act"] == "delete"){ // ส่วนของการลบสินค้าในตะกร้า
	if(isset($_GET["line"]) && isset($_GET["product_id"])){
		unset($_SESSION["product_id"][$_GET["line"]]);
		unset($_SESSION["qty"][$_GET["line"]]);
    }
		
		header("location:./");
}elseif(isset($_GET["act"]) && $_GET["act"] == "cancel"){
    unset($_SESSION["intline"]);
    unset($_SESSION["product_id"]);
    unset($_SESSION["qty"]);
    header('location:./');
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <style>
        input[type='number'] {
            border: 0px;
        }
    </style>
</head>

<body onload="startTime()">

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">POS</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="#">ขาย <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="javascript:void(0);" data-toggle="modal" data-target="#exampleModal">โต๊ะ</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        ตั้งค่า
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="#">ยังไม่มีอะไรจ้า</a>
                    </div>
                </li>
            </ul>


            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link">
                        <div id="txt"></div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">ออกจากระบบ</a>
                </li>
            </ul>
        </div>
    </nav>

    <section class="pt-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8 order-lg-1 order-2">
                    <div class="card">
                        <div class="card-header">
                            รายการอาหาร
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $sql =  "SELECT * FROM products WHERE 1";
                                $result = $con->query($sql);
                                $arr_product = array();
                                while($rows = $result->fetch_assoc()){
                                    $arr_product[$rows["product_id"]] = $rows;
                            ?>
                                <div class="col-lg-3">
                                    <div class="card">
                                        <img src="http://f.ptcdn.info/637/042/000/o747olx8ixmagB3OkHD-o.jpg"
                                            class="card-img-top" alt="...">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo $rows["product_name"];?></h5>
                                            <a href="?act=add&product_id=<?php echo $rows["product_id"];?>"
                                                class="btn btn-primary btn-block">เพิ่ม</a>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 order-1">
                    <div class="card">
                        <div class="card-header">
                            <div class="table-no d-inline-block">
                                โต๊ะที่ <span>(ยังไม่เลือกโต๊ะ)</span>
                            </div>
                            <div class="order-date d-inline-block float-right">
                                <?php echo date("Y-m-d");?>
                            </div>
                        </div>
                        <div class="card-body">
                            <table id="order" class="table">
                                <thead>
                                    <tr>
                                        <th>รายการ</th>
                                        <th class="text-right">จำนวน</th>
                                        <th class="text-right">ราคา</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                $total = 0;
                                if(isset($_SESSION["intLine"])){
                                for($i=0; $i<=(int)$_SESSION["intLine"]; $i++){
                                    if(!empty($_SESSION["product_id"][$i])){
                                        $total += $arr_product[$_SESSION["product_id"][$i]]["product_price"] * $_SESSION["qty"][$i];
                                ?>
                                    <tr>
                                        <td><a class="text-danger"
                                                href="?act=delete&product_id=<?php echo $_SESSION["product_id"][$i];?>&line=<?php echo $i;?>">ลบ</a>
                                            <?php echo $arr_product[$_SESSION["product_id"][$i]]["product_name"];?></td>
                                        <td class="text-right"><input type="number" name="" id=""
                                                value="<?php echo $_SESSION["qty"][$i];?>"
                                                style=" text-align: right;  width: 80px;"></td>
                                        <td class="text-right">
                                            ฿<?php echo $arr_product[$_SESSION["product_id"][$i]]["product_price"];?>
                                        </td>
                                    </tr>
                                    <?php }}} ?>
                                    <tr>
                                        <td class="text-right" colspan="2">รวม</td>
                                        <td class="text-right">฿<?php echo $total;?></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button class="btn btn-success btn-lg btn-block">สั่งออเดอร์</button>
                            <button class="btn btn-danger btn-lg btn-block"
                                onClick="window.location='?act=cancel'">ยกเลิก</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">เลือกโต๊ะ</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row">
                        <?php
                            $sql =  "SELECT * FROM tables WHERE 1";
                            $result = $con->query($sql);
                            while($rows = $result->fetch_assoc()){
                        ?>
                            <div class="col-2 pb-2"><button class="btn btn-info btn-block btn-table-no" data-table-no="<?php echo $rows["table_no"];?>"><?php echo $rows["table_no"];?></button></div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script>
        function startTime() {
            var today = new Date();
            var h = today.getHours();
            var m = today.getMinutes();
            var s = today.getSeconds();
            m = checkTime(m);
            s = checkTime(s);
            document.getElementById('txt').innerHTML =
                h + ":" + m + ":" + s;
            var t = setTimeout(startTime, 500);
        }

        function checkTime(i) {
            if (i < 10) {
                i = "0" + i
            }; // add zero in front of numbers < 10
            return i;
        }

        $(".btn-table-no").on("click", function(){
            $(".table-no span").text($(this).attr("data-table-no"));
            $("#exampleModal").modal('hide')
        })
    </script>
</body>

</html>