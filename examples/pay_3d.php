<?php
    
    use Hasokeyk\parampos_php\parampos_php;
    
    require "vendor/autoload.php";
    
    $parampos = new parampos_php(true);
    
    $order_id = rand(111111,999999);
    
    if(isset($_GET['action'])){
        print_r($_POST);
    }else{
        
        $pay_response = $parampos->pay_3d('Test','4546711234567894','12','26','000','','','1',$order_id);
        if ($pay_response->Pos_OdemeResult->Sonuc > 0) {
            echo "<script>window.top.location='".$pay_response->Pos_OdemeResult->UCD_URL."'</script>";
        } else {
            ?>
            <script>
                alert("Hata meydana geldi.")
            </script>
            <?php
            
        }
        
    }