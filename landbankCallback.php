<?php 
    require_once "model/credit_card.php";
    require_once "include/landBankApi.php";
    require_once "model/checkoutLatest.php";  
    $checkout = new CheckoutLatest();
    $model_CR= new credit_card();
    $LandBank=new LandBank;
    $credentials =$LandBank->getLandBankUAT();
    $LanBankDomain =$LandBank->LanBankDomain();
    $LandBankUrl = $LanBankDomain['sandbox'];
    $lb_order_id=$_GET['orderId'];
    $order_id=0;
    $landBankData=$checkout->getlandbankData($lb_order_id);
    if(count($landBankData)){
        $order_id=$landBankData['order_id'];
        $customerDet=$checkout->getCustomerDet($order_id);
    } 
     $useragent=$_SERVER['HTTP_USER_AGENT'];   
     if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
    {
         $url="https://mb.pesoapp.ph/tabs/home/".$customerDet['telephone'];
       
    }else{
       
         $url="home.php";
    }
    //get lanbank token
    $token="";
    $tokenRequestData = array(
        'clientId' => $credentials['ClientID'],
        'secret' => $credentials['Secret'],
    );
    $lburltoken = curl_init($LandBankUrl.'/api/Ecommerce/Token');
    curl_setopt($lburltoken, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($lburltoken, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Identity: 6d5a74c940694d668aaaae6b402b4ee71cca906bc6ce48c39002fae9d72ae384-IDTK45E291-112701'
    ));
    curl_setopt($lburltoken, CURLOPT_POST, 1);
    curl_setopt($lburltoken, CURLOPT_POSTFIELDS,json_encode($tokenRequestData));
    curl_setopt($lburltoken, CURLOPT_FOLLOWLOCATION, 1);
    $datalburltoken = curl_exec($lburltoken);
    curl_close($lburltoken);
    $Responsedatatoken = json_decode($datalburltoken);
    $token=$Responsedatatoken->body->token;

    //Get Order Status$quadxdata->tracking_number
    $OrderStatusRequestData = array(
        'clientId' => $credentials['ClientID'],
        'secret' => $credentials['Secret'],
        'orderId' => $lb_order_id,  
    );
    $lburl3 = curl_init($LandBankUrl.'/api/PaymentGateway/OrderStatus');
    curl_setopt($lburl3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($lburl3, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ));
    curl_setopt($lburl3, CURLOPT_POST, 1);
    curl_setopt($lburl3, CURLOPT_POSTFIELDS,json_encode($OrderStatusRequestData));
    curl_setopt($lburl3, CURLOPT_FOLLOWLOCATION, 1);
    $datalburl3 = curl_exec($lburl3);
    curl_close($lburl3);
    $Responsedata3 = json_decode($datalburl3);
    $Faicon="";
    $remarks="";
    $btnContinue="";
    if($Responsedata3->code=="200"){
        if($Responsedata3->body->code=="00"){
            //lanbank pay Successful
            $model_CR->addOrderHistory($order_id); 
            $Faicon='<i class="fas fa-check-circle text-success" style="font-size: 70px;"></i>';
            $remarks='<h1 class="text-success text-center" style="font-size: 50px">'.$Responsedata3->body->remarks.'</h1>';
            $btnContinue='<a href="'.$url.'" class="btn btn-success">Continue</a>';
        }else{
             // lanbank pay Unsuccessful
            $Faicon='<i class="fas fa-times-circle text-danger" style="font-size: 70px;"></i>';
            $remarks='<h1 class="text-danger text-center" style="font-size: 50px">'.$Responsedata3->body->remarks.'</h1>'; 
            $btnContinue='<a href="'.$url.'" class="btn btn-danger">Continue</a>';          
        }
    }else{
        $Faicon='<i class="fas fa-times-circle text-danger" style="font-size: 70px;"></i>';
        $remarks='<h1 class="text-danger text-center" style="font-size: 50px">'.$Responsedata3->description.'</h1>';
        $btnContinue='<a href="'.$url.'" class="btn btn-danger">Continue</a>';  

    }
    
?><style>
  .logo-bg {
    background: red;
    border-radius: 50%;
    width: 200px;
    height: 200px;
  }

  .logo-bg img {
    object-fit: contain;
    width: 180px;
    height: 180px;
    margin: 0 0 5px 1px;
  }
</style>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" href="https://mb.pesoapp.ph/assets/icon/favicon.ico" type="image/x-icon" />
  <title>100% Filipino Owned Electronic Store App I Full Warranty Shopping</title>

</head>

<body class="d-flex justify-content-center align-items-center">
  <div class="container">
    <div class="row justify-content-center my-3 mx-3">
      <div class="col-xxl-5 col-xl-6 col-lg-8 col-md-10 col-sm-10 rounded-3 py-3 shadow-lg">
        <div class="row justify-content-center">
          <div class="col-12">
            <div class="logo-bg mx-auto d-flex justify-content-center align-items-center">
               <a href="<?php echo $url;?>"><img src="assets/img/logo-clean.png" alt="logo"></a>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-12" id="imgParent">
            <div class="container">               
                <div class="card">
                    <div class="card-content">
                        <div class="text-center py-5"> 
                            <?php echo  $Faicon;?>
                            <?php echo  $remarks;?>                                  
                        </div>
                        <div class="py-5 text-center">
                            <?php echo  $btnContinue;?>
                        </div>
                    </div>
                </div>
            </div>   
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js" integrity="sha512-csNcFYJniKjJxRWRV1R7fvnXrycHP6qDR21mgz1ZP55xY5d+aHLfo9/FcGDQLfn2IfngbAHd8LdfsagcCqgTcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</html>